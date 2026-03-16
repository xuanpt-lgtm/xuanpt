// ============================================
// TaskFlow Pro v2 — Google Apps Script Backend
// Database: Google Sheets + Google Drive (file uploads)
// ============================================

const SPREADSHEET_ID = "";
const TASKS_SHEET = "Tasks";
const SETTINGS_SHEET = "Settings";
const UPLOAD_FOLDER_NAME = "TaskFlow Attachments";

// ============================================
// WEB APP ENTRY
// ============================================
function doGet() {
  return HtmlService.createHtmlOutputFromFile('index')
    .setTitle('TaskFlow Pro')
    .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL)
    .addMetaTag('viewport', 'width=device-width, initial-scale=1');
}

// ============================================
// SPREADSHEET HELPER
// ============================================
function getSpreadsheet_() {
  var ss = null;
  if (SPREADSHEET_ID && SPREADSHEET_ID !== "") {
    return SpreadsheetApp.openById(SPREADSHEET_ID);
  }
  var props = PropertiesService.getUserProperties();
  var savedId = props.getProperty('TASKFLOW_SHEET_ID');
  if (savedId) {
    try { ss = SpreadsheetApp.openById(savedId); } catch (e) { ss = null; }
  }
  if (!ss) {
    ss = SpreadsheetApp.create('TaskFlow Pro Database');
    props.setProperty('TASKFLOW_SHEET_ID', ss.getId());
    Logger.log('Created new Sheet: ' + ss.getUrl());
  }
  return ss;
}

// ============================================
// INIT SHEETS
// ============================================
function initSheets_() {
  var ss = getSpreadsheet_();

  // --- Tasks sheet ---
  var tasksSheet = ss.getSheetByName(TASKS_SHEET);
  if (!tasksSheet) {
    tasksSheet = ss.insertSheet(TASKS_SHEET);
    tasksSheet.appendRow([
      'id', 'title', 'description', 'category', 'priority', 'status',
      'deadline', 'createdAt', 'checklist', 'notes', 'tables', 'attachments'
    ]);
    tasksSheet.getRange(1, 1, 1, 12).setFontWeight('bold');
    tasksSheet.setFrozenRows(1);
    tasksSheet.setColumnWidth(1, 140);
    tasksSheet.setColumnWidth(2, 250);
    tasksSheet.setColumnWidth(3, 250);
    tasksSheet.setColumnWidth(4, 100);
    tasksSheet.setColumnWidth(5, 80);
    tasksSheet.setColumnWidth(6, 80);
    tasksSheet.setColumnWidth(7, 110);
    tasksSheet.setColumnWidth(8, 160);
    tasksSheet.setColumnWidth(9, 300);
    tasksSheet.setColumnWidth(10, 300);
    tasksSheet.setColumnWidth(11, 300);
    tasksSheet.setColumnWidth(12, 300);
  } else {
    // Migrate: ensure new columns exist
    var headers = tasksSheet.getRange(1, 1, 1, tasksSheet.getLastColumn()).getValues()[0];
    var newCols = ['checklist', 'notes', 'tables', 'attachments'];
    for (var i = 0; i < newCols.length; i++) {
      if (headers.indexOf(newCols[i]) === -1) {
        var nextCol = headers.length + 1;
        tasksSheet.getRange(1, nextCol).setValue(newCols[i]).setFontWeight('bold');
        tasksSheet.setColumnWidth(nextCol, 300);
        headers.push(newCols[i]);
      }
    }
  }

  // --- Settings sheet ---
  var settingsSheet = ss.getSheetByName(SETTINGS_SHEET);
  if (!settingsSheet) {
    settingsSheet = ss.insertSheet(SETTINGS_SHEET);
    settingsSheet.appendRow(['key', 'value']);
    settingsSheet.appendRow(['categories', JSON.stringify(["Công việc", "Cá nhân", "Học tập", "Dự án"])]);
    settingsSheet.appendRow(['darkMode', 'false']);
    settingsSheet.getRange(1, 1, 1, 2).setFontWeight('bold');
    settingsSheet.setFrozenRows(1);
  }

  var defaultSheet = ss.getSheetByName('Sheet1');
  if (defaultSheet && ss.getSheets().length > 1) {
    try { ss.deleteSheet(defaultSheet); } catch (e) {}
  }
  return ss;
}

// ============================================
// COLUMN INDEX HELPER
// ============================================
function getColIndex_(headers, colName) {
  var idx = headers.indexOf(colName);
  return idx >= 0 ? idx : -1;
}

// ============================================
// TASKS CRUD
// ============================================
function getTasks() {
  var ss = initSheets_();
  var sheet = ss.getSheetByName(TASKS_SHEET);
  var data = sheet.getDataRange().getValues();
  if (data.length <= 1) return [];

  var headers = data[0];
  var tasks = [];
  for (var i = 1; i < data.length; i++) {
    var row = data[i];
    if (!row[0]) continue;
    tasks.push(rowToTask_(headers, row));
  }
  return tasks;
}

function rowToTask_(headers, row) {
  return {
    id: String(row[getColIndex_(headers, 'id')] || ''),
    title: String(row[getColIndex_(headers, 'title')] || ''),
    description: String(row[getColIndex_(headers, 'description')] || ''),
    category: String(row[getColIndex_(headers, 'category')] || ''),
    priority: String(row[getColIndex_(headers, 'priority')] || 'medium'),
    status: String(row[getColIndex_(headers, 'status')] || 'todo'),
    deadline: row[getColIndex_(headers, 'deadline')] ? String(row[getColIndex_(headers, 'deadline')]) : null,
    createdAt: String(row[getColIndex_(headers, 'createdAt')] || ''),
    checklist: safeParse_(row[getColIndex_(headers, 'checklist')], []),
    notes: String(row[getColIndex_(headers, 'notes')] || ''),
    tables: safeParse_(row[getColIndex_(headers, 'tables')], []),
    attachments: safeParse_(row[getColIndex_(headers, 'attachments')], [])
  };
}

function addTask(taskData) {
  var ss = initSheets_();
  var sheet = ss.getSheetByName(TASKS_SHEET);
  var headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];

  var id = String(new Date().getTime());
  var createdAt = new Date().toISOString();

  var rowData = [];
  for (var i = 0; i < headers.length; i++) {
    var col = headers[i];
    if (col === 'id') rowData.push(id);
    else if (col === 'createdAt') rowData.push(createdAt);
    else if (col === 'checklist') rowData.push(JSON.stringify(taskData.checklist || []));
    else if (col === 'tables') rowData.push(JSON.stringify(taskData.tables || []));
    else if (col === 'attachments') rowData.push(JSON.stringify(taskData.attachments || []));
    else if (col === 'notes') rowData.push(taskData.notes || '');
    else rowData.push(taskData[col] || '');
  }

  sheet.appendRow(rowData);

  return {
    id: id,
    title: taskData.title || '',
    description: taskData.description || '',
    category: taskData.category || 'Công việc',
    priority: taskData.priority || 'medium',
    status: taskData.status || 'todo',
    deadline: taskData.deadline || null,
    createdAt: createdAt,
    checklist: taskData.checklist || [],
    notes: taskData.notes || '',
    tables: taskData.tables || [],
    attachments: taskData.attachments || []
  };
}

function updateTask(taskData) {
  var ss = initSheets_();
  var sheet = ss.getSheetByName(TASKS_SHEET);
  var data = sheet.getDataRange().getValues();
  var headers = data[0];

  for (var i = 1; i < data.length; i++) {
    if (String(data[i][0]) === String(taskData.id)) {
      var row = i + 1;
      var values = [];
      for (var j = 0; j < headers.length; j++) {
        var col = headers[j];
        if (col === 'id' || col === 'createdAt') {
          values.push(data[i][j]); // keep original
        } else if (col === 'checklist' || col === 'tables' || col === 'attachments') {
          values.push(JSON.stringify(taskData[col] || []));
        } else {
          values.push(taskData[col] !== undefined ? taskData[col] : data[i][j]);
        }
      }
      sheet.getRange(row, 1, 1, headers.length).setValues([values]);
      return { success: true };
    }
  }
  return { success: false, error: 'Task not found: ' + taskData.id };
}

function deleteTask(taskId) {
  var ss = initSheets_();
  var sheet = ss.getSheetByName(TASKS_SHEET);
  var data = sheet.getDataRange().getValues();
  for (var i = 1; i < data.length; i++) {
    if (String(data[i][0]) === String(taskId)) {
      sheet.deleteRow(i + 1);
      return { success: true };
    }
  }
  return { success: false };
}

// ============================================
// FILE UPLOAD TO GOOGLE DRIVE
// ============================================
function getUploadFolder_() {
  var folders = DriveApp.getFoldersByName(UPLOAD_FOLDER_NAME);
  if (folders.hasNext()) return folders.next();
  return DriveApp.createFolder(UPLOAD_FOLDER_NAME);
}

function uploadFile(fileData, fileName, mimeType) {
  var folder = getUploadFolder_();
  var blob = Utilities.newBlob(
    Utilities.base64Decode(fileData),
    mimeType,
    fileName
  );
  var file = folder.createFile(blob);
  file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);

  return {
    name: fileName,
    url: file.getUrl(),
    type: 'file',
    mimeType: mimeType,
    size: file.getSize(),
    driveId: file.getId()
  };
}

// ============================================
// SETTINGS
// ============================================
function getSettings() {
  var ss = initSheets_();
  var sheet = ss.getSheetByName(SETTINGS_SHEET);
  var data = sheet.getDataRange().getValues();
  var settings = {};
  for (var i = 1; i < data.length; i++) {
    settings[data[i][0]] = data[i][1];
  }
  return {
    categories: safeParse_(settings.categories, ["Công việc","Cá nhân","Học tập","Dự án"]),
    darkMode: settings.darkMode === 'true'
  };
}

function saveSettings(settingsData) {
  var ss = initSheets_();
  var sheet = ss.getSheetByName(SETTINGS_SHEET);
  var data = sheet.getDataRange().getValues();
  var updates = {
    categories: JSON.stringify(settingsData.categories),
    darkMode: String(settingsData.darkMode)
  };
  for (var i = 1; i < data.length; i++) {
    var key = data[i][0];
    if (updates[key] !== undefined) {
      sheet.getRange(i + 1, 2).setValue(updates[key]);
    }
  }
  return { success: true };
}

// ============================================
// UTILITIES
// ============================================
function getAllData() {
  return { tasks: getTasks(), settings: getSettings() };
}

function getSheetUrl() {
  return initSheets_().getUrl();
}

function safeParse_(jsonStr, fallback) {
  if (!jsonStr) return fallback;
  try { return JSON.parse(jsonStr); }
  catch (e) { return fallback; }
}

function testInit() {
  var ss = initSheets_();
  Logger.log('Sheet URL: ' + ss.getUrl());
  Logger.log('Tasks: ' + JSON.stringify(getTasks()));
}