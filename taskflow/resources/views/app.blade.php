<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>TaskFlow Pro</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --bg:#fafafa;--surface:#fff;--surface2:#f5f5f5;
  --border:#e5e5e5;--border2:#ddd;
  --text:#111;--text2:#555;--text3:#999;--text-inv:#fff;
  --accent:#111;--accent-hover:#333;
  --red:#e54;--orange:#f90;--green:#2a2;--blue:#37f;
  --red-bg:#fef2f2;--orange-bg:#fff8f0;--green-bg:#f0fdf4;--blue-bg:#eff6ff;
  --radius:10px;--radius-sm:6px;
  --shadow:0 1px 3px rgba(0,0,0,.05);--shadow-md:0 4px 12px rgba(0,0,0,.08);
  --font:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
  --transition:.2s ease;
}
.dark{
  --bg:#0a0a0a;--surface:#161616;--surface2:#1e1e1e;
  --border:#2a2a2a;--border2:#333;
  --text:#eee;--text2:#aaa;--text3:#666;--text-inv:#111;
  --accent:#fff;--accent-hover:#ccc;
  --red-bg:#2a1515;--orange-bg:#2a2010;--green-bg:#152a15;--blue-bg:#151f2a;
  --shadow:0 1px 3px rgba(0,0,0,.3);--shadow-md:0 4px 12px rgba(0,0,0,.4);
}
body{font-family:var(--font);background:var(--bg);color:var(--text);line-height:1.5;min-height:100vh;transition:background var(--transition),color var(--transition)}
button{font-family:var(--font);cursor:pointer}
input,select,textarea{font-family:var(--font);color:var(--text)}

/* LOADING */
.loading-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:var(--bg);display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:999;transition:opacity .3s}
.loading-overlay.hidden{opacity:0;pointer-events:none}
.spinner{width:32px;height:32px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.loading-text{margin-top:16px;font-size:.85rem;color:var(--text2)}

/* TOAST */
.toast-container{position:fixed;top:20px;right:20px;z-index:200;display:flex;flex-direction:column;gap:8px}
.toast{padding:12px 20px;border-radius:var(--radius-sm);font-size:.8rem;box-shadow:var(--shadow-md);animation:slideIn .3s;max-width:320px}
.toast-success{background:#065f46;color:#fff}
.toast-error{background:#991b1b;color:#fff}
.toast-info{background:var(--surface);color:var(--text);border:1px solid var(--border)}
@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}

/* LAYOUT */
.app{max-width:1200px;margin:0 auto;padding:20px;display:none}
.app.loaded{display:block}

/* HEADER */
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.header-left h1{font-size:1.4rem;font-weight:700;letter-spacing:-.03em;display:flex;align-items:center;gap:8px}
.header-left h1 em{font-weight:300;color:var(--text3);font-size:.9rem;font-style:normal}
.header-left p{color:var(--text2);font-size:.8rem;margin-top:2px}
.header-right{display:flex;align-items:center;gap:8px}
.icon-btn{width:36px;height:36px;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:1rem;transition:all var(--transition);color:var(--text2)}
.icon-btn:hover{background:var(--surface2);color:var(--text)}
.sync-indicator{font-size:.7rem;color:var(--text3);display:flex;align-items:center;gap:4px}
.sync-dot{width:6px;height:6px;border-radius:50%;background:var(--green)}
.sync-dot.syncing{background:var(--orange);animation:pulse 1s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}

/* STATS */
.stats-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;transition:all var(--transition)}
.stat-card:hover{box-shadow:var(--shadow-md)}
.stat-value{font-size:1.5rem;font-weight:700}
.stat-label{font-size:.75rem;color:var(--text3);margin-top:2px}
.stat-bar{height:3px;background:var(--surface2);border-radius:2px;margin-top:10px;overflow:hidden}
.stat-bar-fill{height:100%;border-radius:2px;transition:width .4s ease}

/* TOOLBAR */
.toolbar{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
.search-box{flex:1;min-width:200px;display:flex;align-items:center;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;transition:border var(--transition)}
.search-box:focus-within{border-color:#999}
.search-box svg{flex-shrink:0;color:var(--text3)}
.search-box input{flex:1;border:none;outline:none;padding:9px 8px;font-size:.85rem;background:transparent}
.filters-row{display:flex;gap:4px;flex-wrap:wrap}
.filter-btn{padding:7px 14px;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surface);font-size:.8rem;color:var(--text2);transition:all var(--transition);white-space:nowrap}
.filter-btn:hover{border-color:#999}
.filter-btn.active{background:var(--accent);color:var(--text-inv);border-color:var(--accent)}
.view-toggle{display:flex;border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden}
.view-btn{padding:7px 14px;border:none;background:var(--surface);font-size:.8rem;color:var(--text2);transition:all var(--transition)}
.view-btn.active{background:var(--accent);color:var(--text-inv)}
.view-btn:not(:last-child){border-right:1px solid var(--border)}

/* ADD TASK */
.add-task-bar{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;margin-bottom:24px;box-shadow:var(--shadow)}
.add-task-row{display:flex;gap:8px;flex-wrap:wrap}
.add-task-bar input[type="text"]{flex:1;min-width:200px;border:1px solid var(--border);outline:none;padding:9px 12px;border-radius:var(--radius-sm);font-size:.85rem;background:var(--surface2);transition:border var(--transition)}
.add-task-bar input[type="text"]:focus{border-color:#999}
.add-task-bar input[type="date"],.add-task-bar select{border:1px solid var(--border);outline:none;padding:9px 12px;border-radius:var(--radius-sm);font-size:.8rem;background:var(--surface2)}
.btn-primary{padding:9px 20px;background:var(--accent);color:var(--text-inv);border:none;border-radius:var(--radius-sm);font-size:.85rem;font-weight:500;transition:opacity var(--transition)}
.btn-primary:hover{opacity:.8}
.btn-primary:disabled{opacity:.4;cursor:not-allowed}
.btn-secondary{padding:9px 18px;background:var(--surface2);color:var(--text);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:.85rem;transition:all var(--transition)}
.btn-secondary:hover{background:var(--border)}
.btn-danger{padding:9px 18px;background:var(--red);color:#fff;border:none;border-radius:var(--radius-sm);font-size:.85rem;transition:opacity var(--transition)}
.btn-danger:hover{opacity:.8}
.btn-sm{padding:5px 12px;font-size:.75rem;border-radius:4px}

/* KANBAN */
.kanban{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.kanban-col{background:var(--surface2);border-radius:var(--radius);padding:12px;min-height:300px;transition:all var(--transition)}
.kanban-col-header{display:flex;align-items:center;justify-content:space-between;padding:8px 4px;margin-bottom:8px;font-size:.8rem;font-weight:600}
.kanban-col-count{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:1px 8px;font-size:.7rem;color:var(--text2);font-weight:400}
.kanban-col.drag-over{background:var(--blue-bg);outline:2px dashed var(--blue)}

/* TASK CARD */
.task-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:12px 14px;margin-bottom:8px;cursor:grab;transition:all var(--transition);position:relative}
.task-card:hover{box-shadow:var(--shadow-md);border-color:var(--border2)}
.task-card.dragging{opacity:.4;transform:rotate(2deg)}
.task-card-top{display:flex;align-items:flex-start;gap:10px}
.task-card-check{width:18px;height:18px;border-radius:50%;border:2px solid var(--border);cursor:pointer;flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center;transition:all var(--transition);background:transparent}
.task-card-check:hover{border-color:#999}
.task-card.done .task-card-check{background:var(--accent);border-color:var(--accent)}
.task-card.done .task-card-check::after{content:"\2713";color:var(--text-inv);font-size:.65rem;font-weight:700}
.task-card.done .task-card-title{text-decoration:line-through;color:var(--text3)}
.task-card-body{flex:1;min-width:0;cursor:pointer}
.task-card-title{font-size:.85rem;font-weight:500;word-break:break-word}
.task-card-desc{font-size:.75rem;color:var(--text2);margin-top:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.task-card-meta{display:flex;align-items:center;gap:6px;margin-top:8px;flex-wrap:wrap}
.badge{font-size:.65rem;padding:2px 8px;border-radius:10px;font-weight:500}
.badge-cat{background:var(--surface2);color:var(--text2);border:1px solid var(--border)}
.badge-high{background:var(--red-bg);color:var(--red)}
.badge-medium{background:var(--orange-bg);color:var(--orange)}
.badge-low{background:var(--green-bg);color:var(--green)}
.badge-date{font-size:.7rem;color:var(--text3);display:flex;align-items:center;gap:3px}
.badge-date.overdue{color:var(--red);font-weight:500}
.badge-extra{font-size:.6rem;padding:2px 6px;border-radius:6px;background:var(--surface2);color:var(--text3);border:1px solid var(--border)}
.task-card-actions{position:absolute;top:8px;right:8px;display:flex;gap:2px;opacity:0;transition:opacity var(--transition)}
.task-card:hover .task-card-actions{opacity:1}
.task-card-actions button{background:var(--surface2);border:none;width:26px;height:26px;border-radius:4px;font-size:.7rem;color:var(--text2);display:flex;align-items:center;justify-content:center;transition:all var(--transition)}
.task-card-actions button:hover{background:var(--border);color:var(--text)}

/* LIST VIEW */
.list-view{display:flex;flex-direction:column;gap:4px}
.list-view .task-card{cursor:default;margin-bottom:0}
.empty-state{text-align:center;padding:60px 20px;color:var(--text3);font-size:.9rem}
.empty-icon{font-size:2.5rem;margin-bottom:12px;opacity:.25}

/* MODAL */
.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);display:none;align-items:center;justify-content:center;z-index:100;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:12px;padding:28px;width:90%;max-width:480px;box-shadow:var(--shadow-md);max-height:90vh;overflow-y:auto}
.modal h2{font-size:1.1rem;font-weight:600;margin-bottom:20px}
.modal-field{margin-bottom:16px}
.modal-field label{display:block;font-size:.8rem;color:var(--text2);margin-bottom:6px;font-weight:500}
.modal-field input,.modal-field select,.modal-field textarea{width:100%;border:1px solid var(--border);border-radius:var(--radius-sm);padding:9px 12px;font-size:.85rem;background:var(--surface2);outline:none;transition:border var(--transition)}
.modal-field input:focus,.modal-field select:focus,.modal-field textarea:focus{border-color:#999}
.modal-field textarea{resize:vertical;min-height:80px}
.modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:24px}

/* DETAIL MODAL */
.detail-modal{background:var(--surface);border-radius:12px;width:92%;max-width:640px;box-shadow:var(--shadow-md);max-height:92vh;overflow-y:auto;display:flex;flex-direction:column}
.detail-header{padding:24px 28px 16px;display:flex;align-items:flex-start;gap:12px;border-bottom:1px solid var(--border)}
.detail-check{width:24px;height:24px;border-radius:50%;border:2px solid var(--border);cursor:pointer;flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center;transition:all var(--transition);background:transparent}
.detail-check:hover{border-color:#999}
.detail-check.checked{background:var(--accent);border-color:var(--accent)}
.detail-check.checked::after{content:"\2713";color:var(--text-inv);font-size:.75rem;font-weight:700}
.detail-title-area{flex:1;min-width:0}
.detail-title{font-size:1.1rem;font-weight:600;word-break:break-word;line-height:1.4}
.detail-title.done-title{text-decoration:line-through;color:var(--text3)}
.detail-created{font-size:.7rem;color:var(--text3);margin-top:4px}
.detail-body{padding:20px 28px;flex:1;overflow-y:auto}
.detail-badges{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.detail-badge{font-size:.75rem;padding:4px 12px;border-radius:14px;font-weight:500;display:flex;align-items:center;gap:4px}
.detail-badge-status{background:var(--surface2);color:var(--text2);border:1px solid var(--border)}
.detail-badge-status.status-todo{border-color:var(--blue);color:var(--blue)}
.detail-badge-status.status-doing{border-color:var(--orange);color:var(--orange)}
.detail-badge-status.status-done{border-color:var(--green);color:var(--green)}
.detail-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px}
.detail-info-item{background:var(--surface2);border-radius:var(--radius-sm);padding:10px 14px}
.detail-info-label{font-size:.65rem;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px}
.detail-info-value{font-size:.85rem;font-weight:500}
.detail-info-value.overdue-text{color:var(--red)}
.detail-section{margin-bottom:20px}
.detail-section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.detail-section-label{font-size:.75rem;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;font-weight:600}
.detail-section-actions{display:flex;gap:4px}

/* RICH TEXT EDITOR */
.rte-toolbar{display:flex;gap:2px;padding:6px 8px;background:var(--surface2);border:1px solid var(--border);border-bottom:none;border-radius:var(--radius-sm) var(--radius-sm) 0 0;flex-wrap:wrap}
.rte-btn{width:30px;height:28px;border:none;background:transparent;border-radius:4px;font-size:.8rem;color:var(--text2);display:flex;align-items:center;justify-content:center;transition:all .15s}
.rte-btn:hover{background:var(--border);color:var(--text)}
.rte-btn.active{background:var(--accent);color:var(--text-inv)}
.rte-sep{width:1px;background:var(--border);margin:4px 4px}
.rte-editor{min-height:120px;max-height:300px;overflow-y:auto;padding:12px;border:1px solid var(--border);border-radius:0 0 var(--radius-sm) var(--radius-sm);background:var(--surface);font-size:.85rem;line-height:1.6;outline:none}
.rte-editor:focus{border-color:#999}
.rte-editor h3{font-size:.95rem;margin:8px 0 4px}
.rte-editor ul,.rte-editor ol{padding-left:20px;margin:4px 0}
.rte-editor blockquote{border-left:3px solid var(--border);padding-left:12px;color:var(--text2);margin:8px 0}

/* CHECKLIST */
.checklist-item{display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--surface2)}
.checklist-item:last-child{border-bottom:none}
.cl-check{width:16px;height:16px;border-radius:4px;border:2px solid var(--border);cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;background:transparent}
.cl-check:hover{border-color:#999}
.cl-check.checked{background:var(--accent);border-color:var(--accent)}
.cl-check.checked::after{content:"\2713";color:var(--text-inv);font-size:.6rem;font-weight:700}
.cl-text{flex:1;font-size:.85rem;border:none;outline:none;background:transparent;color:var(--text);font-family:var(--font)}
.cl-text.done{text-decoration:line-through;color:var(--text3)}
.cl-del{width:20px;height:20px;border:none;background:transparent;color:var(--text3);font-size:.8rem;cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;opacity:0;transition:all .15s}
.checklist-item:hover .cl-del{opacity:1}
.cl-del:hover{background:var(--red-bg);color:var(--red)}
.cl-add{display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer;color:var(--text3);font-size:.8rem;transition:color .15s}
.cl-add:hover{color:var(--text)}
.cl-progress{height:3px;background:var(--surface2);border-radius:2px;margin-top:8px;overflow:hidden}
.cl-progress-fill{height:100%;background:var(--green);border-radius:2px;transition:width .3s}

/* TABLE BUILDER */
.tbl-container{overflow-x:auto;margin-top:4px}
.tbl-table{width:100%;border-collapse:collapse;font-size:.8rem}
.tbl-table th,.tbl-table td{border:1px solid var(--border);padding:6px 10px;text-align:left;min-width:80px}
.tbl-table th{background:var(--surface2);font-weight:600;font-size:.75rem;color:var(--text2)}
.tbl-table td{background:var(--surface)}
.tbl-cell-input{width:100%;border:none;outline:none;background:transparent;font-size:.8rem;font-family:var(--font);color:var(--text);padding:0}
.tbl-actions{display:flex;gap:4px;margin-top:8px;flex-wrap:wrap}

/* ATTACHMENTS */
.att-list{display:flex;flex-direction:column;gap:6px}
.att-item{display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--surface2);border-radius:var(--radius-sm);transition:all .15s}
.att-item:hover{background:var(--border)}
.att-icon{font-size:1.2rem;flex-shrink:0}
.att-info{flex:1;min-width:0}
.att-name{font-size:.8rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.att-name a{color:var(--text);text-decoration:none}
.att-name a:hover{text-decoration:underline}
.att-meta{font-size:.65rem;color:var(--text3)}
.att-del{width:24px;height:24px;border:none;background:transparent;color:var(--text3);font-size:.75rem;cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;opacity:0;transition:all .15s}
.att-item:hover .att-del{opacity:1}
.att-del:hover{background:var(--red-bg);color:var(--red)}
.att-add-row{display:flex;gap:8px;margin-top:8px;flex-wrap:wrap}
.att-add-row input[type="text"]{flex:1;min-width:150px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:7px 10px;font-size:.8rem;background:var(--surface2);outline:none}
.att-upload-label{display:inline-flex;align-items:center;gap:4px;padding:7px 14px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:.8rem;color:var(--text2);cursor:pointer;transition:all .15s}
.att-upload-label:hover{border-color:#999;color:var(--text)}
.att-uploading{font-size:.75rem;color:var(--orange);padding:4px 0}
.detail-footer{padding:16px 28px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end;flex-shrink:0}

/* DASHBOARD */
.dashboard{display:flex;flex-direction:column;gap:20px}
.dash-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.dash-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.dash-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;transition:all var(--transition)}
.dash-card:hover{box-shadow:var(--shadow-md)}
.dash-card-title{font-size:.8rem;font-weight:600;color:var(--text2);margin-bottom:16px;display:flex;align-items:center;gap:6px}
.dash-card-title span{font-size:1rem}
.donut-wrap{display:flex;align-items:center;gap:24px}
.donut-svg{flex-shrink:0}
.donut-legend{display:flex;flex-direction:column;gap:8px}
.donut-legend-item{display:flex;align-items:center;gap:8px;font-size:.8rem}
.donut-legend-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0}
.donut-legend-val{font-weight:600;margin-left:auto;min-width:24px;text-align:right}
.bar-chart{display:flex;align-items:flex-end;gap:8px;height:140px;padding-top:10px}
.bar-group{flex:1;display:flex;flex-direction:column;align-items:center;height:100%;justify-content:flex-end}
.bar-fill{width:100%;max-width:48px;border-radius:4px 4px 0 0;transition:height .4s ease;min-height:2px;position:relative}
.bar-fill:hover{opacity:.85}
.bar-val{font-size:.65rem;font-weight:600;text-align:center;margin-bottom:4px;color:var(--text2)}
.bar-label{font-size:.65rem;color:var(--text3);margin-top:6px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:60px}
.line-chart-wrap{position:relative;height:160px}
.line-chart-svg{width:100%;height:100%}
.line-chart-label{font-size:.6rem;fill:var(--text3)}
.line-chart-grid{stroke:var(--border);stroke-width:.5}
.line-chart-path{fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.line-chart-area{opacity:.1}
.line-chart-dot{r:3;transition:r .15s}
.line-chart-dot:hover{r:5}
.dash-table{width:100%;border-collapse:collapse;font-size:.8rem}
.dash-table th{text-align:left;padding:8px 10px;border-bottom:2px solid var(--border);font-size:.7rem;color:var(--text3);text-transform:uppercase;letter-spacing:.03em}
.dash-table td{padding:8px 10px;border-bottom:1px solid var(--surface2)}
.dash-table tr:hover td{background:var(--surface2)}
.dash-table .td-title{font-weight:500;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;cursor:pointer}
.dash-table .td-title:hover{color:var(--blue);text-decoration:underline}
.dash-table .td-badge{display:inline-block;font-size:.65rem;padding:2px 8px;border-radius:10px;font-weight:500}
.dash-table .td-overdue{color:var(--red);font-weight:500}
.dash-empty{text-align:center;padding:24px;color:var(--text3);font-size:.8rem}
.metric-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:4px}
.metric-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;text-align:center}
.metric-val{font-size:1.8rem;font-weight:700;line-height:1.2}
.metric-label{font-size:.7rem;color:var(--text3);margin-top:4px}
.timeline{position:relative;padding-left:24px}
.timeline::before{content:'';position:absolute;left:8px;top:0;bottom:0;width:2px;background:var(--border)}
.timeline-item{position:relative;padding-bottom:16px}
.timeline-item:last-child{padding-bottom:0}
.timeline-dot{position:absolute;left:-20px;top:2px;width:12px;height:12px;border-radius:50%;border:2px solid var(--surface);z-index:1}
.timeline-dot.dot-done{background:var(--green)}
.timeline-dot.dot-doing{background:var(--orange)}
.timeline-dot.dot-todo{background:var(--blue)}
.timeline-dot.dot-overdue{background:var(--red)}
.timeline-date{font-size:.65rem;color:var(--text3);margin-bottom:2px}
.timeline-title{font-size:.8rem;font-weight:500;cursor:pointer}
.timeline-title:hover{color:var(--blue)}
.timeline-meta{font-size:.7rem;color:var(--text3);margin-top:2px}

/* RESPONSIVE */
@media(max-width:768px){
  .kanban{grid-template-columns:1fr}
  .stats-bar{grid-template-columns:repeat(2,1fr)}
  .header{flex-direction:column;align-items:flex-start}
  .task-card-actions{opacity:1}
  .toolbar{flex-direction:column}
  .search-box{min-width:unset;width:100%}
  .detail-modal{width:96%;max-width:none}
  .detail-info-grid{grid-template-columns:1fr}
  .dash-row,.dash-row-3{grid-template-columns:1fr}
  .metric-cards{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){.app{padding:12px}.stats-bar{grid-template-columns:1fr 1fr}.metric-cards{grid-template-columns:1fr 1fr}}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.task-card{animation:fadeIn .25s ease}
.modal,.detail-modal{animation:slideUp .2s ease}

/* NOTIFICATION BELL */
.notif-btn{position:relative}
.notif-badge{position:absolute;top:-5px;right:-5px;min-width:16px;height:16px;background:var(--red);color:#fff;border-radius:8px;font-size:.6rem;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 3px;pointer-events:none;animation:badgePop .2s ease}
@keyframes badgePop{from{transform:scale(0)}to{transform:scale(1)}}
.notif-bell{font-size:1.1rem;transition:transform .2s}
.notif-btn:hover .notif-bell{transform:rotate(20deg)}
.notif-btn.active-notif{color:var(--orange)}

/* NOTIFICATION SETTINGS MODAL */
.notif-modal{max-width:400px}
.notif-permission-bar{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surface2);margin-bottom:20px;gap:12px}
.notif-permission-label{font-size:.82rem;font-weight:500}
.notif-permission-sub{font-size:.72rem;color:var(--text3);margin-top:2px}
.notif-perm-btn{padding:6px 14px;border-radius:var(--radius-sm);border:none;font-size:.78rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:opacity .15s}
.notif-perm-btn:hover{opacity:.8}
.notif-perm-granted{background:#065f46;color:#fff}
.notif-perm-request{background:var(--accent);color:var(--text-inv)}
.notif-perm-denied{background:#991b1b;color:#fff}
.notif-section-label{font-size:.75rem;font-weight:600;color:var(--text2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px}
.notif-options{display:flex;flex-direction:column;gap:6px;margin-bottom:20px}
.notif-option{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--radius-sm);border:1px solid var(--border);cursor:pointer;transition:all .15s;user-select:none}
.notif-option:hover{border-color:#999;background:var(--surface2)}
.notif-option.selected{border-color:var(--accent);background:var(--surface2)}
.notif-check{width:16px;height:16px;border-radius:4px;border:2px solid var(--border);flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s}
.notif-option.selected .notif-check{background:var(--accent);border-color:var(--accent)}
.notif-option.selected .notif-check::after{content:'\2713';color:var(--text-inv);font-size:.65rem;font-weight:700}
.notif-option-text{font-size:.82rem}
.notif-option-sub{font-size:.7rem;color:var(--text3);margin-top:1px}
.notif-preview{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:20px}
.notif-preview-title{font-size:.72rem;color:var(--text3);margin-bottom:8px;font-weight:500}
.notif-preview-item{display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid var(--border);font-size:.78rem}
.notif-preview-item:last-child{border-bottom:none}
.notif-preview-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.dot-urgent{background:var(--red)}
.dot-soon{background:var(--orange)}
.dot-normal{background:var(--blue)}
.notif-empty-preview{font-size:.78rem;color:var(--text3);text-align:center;padding:8px 0}

/* DETAIL – NOTIFICATION SECTION */
.detail-notif-perm-hint{font-size:.73rem;color:var(--text3);margin-bottom:8px;display:flex;align-items:center;gap:6px}
.btn-link-inline{background:none;border:none;color:var(--blue);font-size:.73rem;font-weight:600;cursor:pointer;padding:0;text-decoration:underline}
.btn-link-inline:hover{opacity:.75}
.detail-notif-chips{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px}
.detail-notif-chip{padding:4px 10px;border-radius:20px;border:1px solid var(--border);font-size:.72rem;cursor:pointer;transition:all .15s;background:var(--surface);color:var(--text2);user-select:none;white-space:nowrap}
.detail-notif-chip:hover{border-color:#999;background:var(--surface2)}
.detail-notif-chip.active{background:var(--accent);color:var(--text-inv);border-color:var(--accent)}
.detail-notif-status-bar{font-size:.76rem;padding:7px 10px;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surface2);color:var(--text2)}
.detail-notif-status-bar.status-active{color:var(--green);border-color:var(--green);background:var(--green-bg)}
.detail-notif-status-bar.status-overdue{color:var(--red);border-color:var(--red);background:var(--red-bg)}
.detail-notif-status-bar.status-done{color:var(--text3);border-color:var(--border)}
</style>
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
  <div class="spinner"></div>
  <div class="loading-text">Đang tải dữ liệu...</div>
</div>
<div class="toast-container" id="toastContainer"></div>

<div class="app" id="app">
  <div class="header">
    <div class="header-left">
      <h1>TaskFlow <em>Pro</em></h1>
      <p id="dateDisplay"></p>
    </div>
    <div class="header-right">
      <div class="sync-indicator">
        <span class="sync-dot" id="syncDot"></span>
        <span id="syncText">Đã lưu</span>
      </div>
      <button class="icon-btn notif-btn" id="notifBtn" onclick="openNotifSettings()" title="Nhắc việc">
        <span class="notif-bell">&#128276;</span>
      </button>
      <button class="icon-btn" onclick="refreshData()" title="Làm mới">&#8635;</button>
      <button class="icon-btn" onclick="toggleTheme()" title="Giao diện">&#9681;</button>
    </div>
  </div>

  <div class="stats-bar" id="statsBar"></div>

  <div class="toolbar">
    <div class="search-box">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Tìm kiếm... (nhấn /)" oninput="renderUI()">
    </div>
    <div class="filters-row" id="categoryFilters"></div>
    <div class="filters-row" id="priorityFilters"></div>
    <div class="view-toggle">
      <button class="view-btn active" data-view="kanban" onclick="setView('kanban')">Kanban</button>
      <button class="view-btn" data-view="list" onclick="setView('list')">Danh sách</button>
      <button class="view-btn" data-view="dashboard" onclick="setView('dashboard')">Dashboard</button>
    </div>
  </div>

  <div class="add-task-bar">
    <div class="add-task-row">
      <input type="text" id="newTitle" placeholder="Tên công việc mới...">
      <select id="newCategory"></select>
      <select id="newPriority">
        <option value="low">Thấp</option>
        <option value="medium" selected>Trung bình</option>
        <option value="high">Cao</option>
      </select>
      <input type="date" id="newDeadline">
      <button class="btn-primary" id="addBtn" onclick="handleAddTask()">+ Thêm</button>
    </div>
  </div>

  <div id="mainContent"></div>
</div>

<!-- DETAIL MODAL -->
<div class="modal-overlay" id="detailModal">
  <div class="detail-modal">
    <div class="detail-header">
      <div class="detail-check" id="detailCheck" onclick="toggleDoneFromDetail()"></div>
      <div class="detail-title-area">
        <div class="detail-title" id="detailTitle"></div>
        <div class="detail-created" id="detailCreated"></div>
      </div>
    </div>
    <div class="detail-body">
      <div class="detail-badges" id="detailBadges"></div>
      <div class="detail-info-grid" id="detailInfoGrid"></div>

      <!-- NHẮC VIỆC -->
      <div class="detail-section" id="detailNotifSection">
        <div class="detail-section-header">
          <span class="detail-section-label">&#128276; Nhắc việc</span>
        </div>
        <div id="detailNotifContent"></div>
      </div>

      <!-- GHI CHÚ RICH TEXT -->
      <div class="detail-section" id="notesSection">
        <div class="detail-section-header">
          <span class="detail-section-label">Ghi chú</span>
        </div>
        <div class="rte-toolbar" id="rteToolbar">
          <button class="rte-btn" onclick="rteCmd('bold')" title="Bold"><b>B</b></button>
          <button class="rte-btn" onclick="rteCmd('italic')" title="Italic"><i>I</i></button>
          <button class="rte-btn" onclick="rteCmd('underline')" title="Underline"><u>U</u></button>
          <button class="rte-btn" onclick="rteCmd('strikeThrough')" title="Gạch ngang"><s>S</s></button>
          <div class="rte-sep"></div>
          <button class="rte-btn" onclick="rteCmd('formatBlock','<h3>')" title="Tiêu đề">H</button>
          <button class="rte-btn" onclick="rteCmd('insertUnorderedList')" title="Danh sách">&#8226;</button>
          <button class="rte-btn" onclick="rteCmd('insertOrderedList')" title="Đánh số">1.</button>
          <button class="rte-btn" onclick="rteCmd('formatBlock','<blockquote>')" title="Trích dẫn">&#8220;</button>
          <div class="rte-sep"></div>
          <button class="rte-btn" onclick="rteCmd('removeFormat')" title="Xóa định dạng">&#10005;</button>
        </div>
        <div class="rte-editor" id="rteEditor" contenteditable="true"></div>
      </div>

      <!-- CHECKLIST -->
      <div class="detail-section" id="checklistSection">
        <div class="detail-section-header">
          <span class="detail-section-label">Checklist</span>
        </div>
        <div id="checklistContainer"></div>
        <div class="cl-progress"><div class="cl-progress-fill" id="clProgress"></div></div>
      </div>

      <!-- BẢNG DỮ LIỆU -->
      <div class="detail-section" id="tableSection">
        <div class="detail-section-header">
          <span class="detail-section-label">Bảng dữ liệu</span>
          <div class="detail-section-actions">
            <button class="btn-secondary btn-sm" onclick="addTable()">+ Bảng mới</button>
          </div>
        </div>
        <div id="tablesContainer"></div>
      </div>

      <!-- ĐÍNH KÈM -->
      <div class="detail-section" id="attachSection">
        <div class="detail-section-header">
          <span class="detail-section-label">Đính kèm</span>
        </div>
        <div class="att-list" id="attList"></div>
        <div class="att-add-row">
          <input type="text" id="attLinkInput" placeholder="Dán link URL...">
          <button class="btn-secondary btn-sm" onclick="addLinkAttachment()">+ Link</button>
          <label class="att-upload-label">
            &#128206; Upload
            <input type="file" id="attFileInput" style="display:none" onchange="handleFileUpload(event)">
          </label>
        </div>
        <div class="att-uploading" id="attUploading" style="display:none">Đang upload file...</div>
      </div>
    </div>
    <div class="detail-footer">
      <button class="btn-danger" onclick="deleteFromDetail()">Xóa</button>
      <button class="btn-secondary" onclick="closeDetailModal()">Đóng</button>
      <button class="btn-primary" onclick="saveDetailAndClose()">Lưu thay đổi</button>
    </div>
  </div>
</div>

<!-- NOTIFICATION SETTINGS MODAL -->
<div class="modal-overlay" id="notifModal">
  <div class="modal notif-modal">
    <h2>&#128276; Cài đặt nhắc việc</h2>

    <!-- Permission bar -->
    <div class="notif-permission-bar" id="notifPermBar">
      <div>
        <div class="notif-permission-label" id="notifPermLabel">Thông báo trình duyệt</div>
        <div class="notif-permission-sub" id="notifPermSub">Cho phép để nhận nhắc nhở</div>
      </div>
      <button class="notif-perm-btn" id="notifPermBtn" onclick="handlePermissionClick()">Cho phép</button>
    </div>

    <!-- Time options -->
    <div class="notif-section-label">Nhắc trước deadline</div>
    <div class="notif-options" id="notifOptions">
      <!-- rendered by JS -->
    </div>

    <!-- Tasks sắp đến hạn preview -->
    <div class="notif-section-label">Tasks sắp đến hạn</div>
    <div class="notif-preview" id="notifPreview">
      <!-- rendered by JS -->
    </div>

    <div class="modal-actions">
      <button class="btn-secondary" onclick="closeNotifSettings()">Đóng</button>
      <button class="btn-primary" onclick="saveNotifSettings()">Lưu cài đặt</button>
    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h2>Chỉnh sửa nhanh</h2>
    <input type="hidden" id="editId">
    <div class="modal-field"><label>Tên</label><input type="text" id="editTitle"></div>
    <div class="modal-field"><label>Mô tả ngắn</label><textarea id="editDesc" rows="2"></textarea></div>
    <div class="modal-field"><label>Danh mục</label><select id="editCategory"></select></div>
    <div class="modal-field">
      <label>Ưu tiên</label>
      <select id="editPriority">
        <option value="low">Thấp</option>
        <option value="medium">Trung bình</option>
        <option value="high">Cao</option>
      </select>
    </div>
    <div class="modal-field">
      <label>Trạng thái</label>
      <select id="editStatus">
        <option value="todo">Cần làm</option>
        <option value="doing">Đang làm</option>
        <option value="done">Hoàn thành</option>
      </select>
    </div>
    <div class="modal-field"><label>Deadline</label><input type="date" id="editDeadline"></div>
    <div class="modal-actions">
      <button class="btn-secondary" onclick="closeModal()">Hủy</button>
      <button class="btn-primary" onclick="handleSaveEdit()">Lưu</button>
    </div>
  </div>
</div>

<script>
// ============================================
// KHỞI TẠO
// ============================================
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let tasks = [], categories = [], currentView = 'kanban';
let activeCategory = 'all', activePriority = 'all', darkMode = false;
let detailTaskId = null;

// Hiển thị ngày hôm nay
(function() {
  const el = document.getElementById('dateDisplay');
  el.textContent = new Date().toLocaleDateString('vi-VN', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
  });
})();

// ============================================
// API HELPER
// ============================================
const API = {
  async request(method, url, data = null) {
    const opts = {
      method,
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
      },
    };
    if (data && method !== 'GET') {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(data);
    }
    const res = await fetch(url, opts);
    if (!res.ok) {
      const err = await res.json().catch(() => ({ message: 'Lỗi máy chủ' }));
      throw new Error(err.message || 'Lỗi không xác định');
    }
    return res.json();
  },
  get: (url) => API.request('GET', url),
  post: (url, data) => API.request('POST', url, data),
  put: (url, data) => API.request('PUT', url, data),
  delete: (url) => API.request('DELETE', url),
};

// ============================================
// TOAST / SYNC
// ============================================
function showToast(msg, type = 'info') {
  const container = document.getElementById('toastContainer');
  const d = document.createElement('div');
  d.className = 'toast toast-' + type;
  d.textContent = msg;
  container.appendChild(d);
  setTimeout(() => {
    d.style.opacity = '0';
    d.style.transition = 'opacity .3s';
    setTimeout(() => d.remove(), 300);
  }, 3000);
}

function setSyncing(v) {
  const dot = document.getElementById('syncDot');
  const txt = document.getElementById('syncText');
  if (v) {
    dot.classList.add('syncing');
    txt.textContent = 'Đang lưu...';
  } else {
    dot.classList.remove('syncing');
    txt.textContent = 'Đã lưu';
  }
}

// ============================================
// TẢI DỮ LIỆU
// ============================================
async function loadData() {
  try {
    const data = await API.get('/api/data');
    tasks = data.tasks || [];
    categories = data.settings.categories || [];
    darkMode = data.settings.darkMode || false;
    notifyBefore = data.settings.notifyBefore || [1440];
    if (darkMode) document.body.classList.add('dark');
    renderFilters();
    renderUI();
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('app').classList.add('loaded');
    updateNotifBadge();
    if (Notification.permission === 'granted') scheduleNotifCheck();
  } catch (e) {
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('app').classList.add('loaded');
    showToast('Lỗi tải dữ liệu: ' + e.message, 'error');
  }
}

async function refreshData() {
  setSyncing(true);
  try {
    const data = await API.get('/api/data');
    tasks = data.tasks || [];
    categories = data.settings.categories || [];
    renderFilters();
    renderUI();
    showToast('Đã làm mới', 'success');
  } catch (e) {
    showToast('Lỗi: ' + e.message, 'error');
  } finally {
    setSyncing(false);
  }
}

async function toggleTheme() {
  darkMode = !darkMode;
  document.body.classList.toggle('dark', darkMode);
  try {
    await API.put('/api/settings', { categories, darkMode });
  } catch (e) {
    // Không ảnh hưởng UX, chỉ bỏ qua
  }
}

function setView(v) {
  currentView = v;
  document.querySelectorAll('.view-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.view === v)
  );
  const isDash = v === 'dashboard';
  document.querySelector('.add-task-bar').style.display = isDash ? 'none' : 'block';
  document.getElementById('statsBar').style.display = isDash ? 'none' : 'grid';
  document.getElementById('categoryFilters').style.display = isDash ? 'none' : 'flex';
  document.getElementById('priorityFilters').style.display = isDash ? 'none' : 'flex';
  renderUI();
}

// ============================================
// FILTERS
// ============================================
function renderFilters() {
  // Category select trong add-task
  const sel = document.getElementById('newCategory');
  sel.innerHTML = categories.map(c =>
    `<option value="${ea(c)}">${eh(c)}</option>`
  ).join('');

  // Category filter buttons
  const cf = document.getElementById('categoryFilters');
  let h = `<button class="filter-btn${activeCategory === 'all' ? ' active' : ''}" onclick="setFilter('cat','all')">Tất cả</button>`;
  categories.forEach(c => {
    h += `<button class="filter-btn${activeCategory === c ? ' active' : ''}" onclick="setFilter('cat','${ea(c)}')">${eh(c)}</button>`;
  });
  h += `<button class="filter-btn" onclick="addCategory()">+</button>`;
  cf.innerHTML = h;

  // Priority filter buttons
  const pf = document.getElementById('priorityFilters');
  const pi = [['all','Mọi mức'],['high','Cao'],['medium','TB'],['low','Thấp']];
  pf.innerHTML = pi.map(([val, label]) =>
    `<button class="filter-btn${activePriority === val ? ' active' : ''}" onclick="setFilter('pri','${val}')">${label}</button>`
  ).join('');
}

function setFilter(type, val) {
  if (type === 'cat') activeCategory = val;
  else activePriority = val;
  renderFilters();
  renderUI();
}

async function addCategory() {
  const n = prompt('Tên danh mục mới:');
  if (n && n.trim() && !categories.includes(n.trim())) {
    categories.push(n.trim());
    setSyncing(true);
    try {
      await API.put('/api/settings', { categories, darkMode });
      showToast('Đã thêm danh mục', 'success');
    } catch (e) {
      showToast('Lỗi', 'error');
    } finally {
      setSyncing(false);
    }
    renderFilters();
  }
}

// ============================================
// THÊM TASK MỚI
// ============================================
async function handleAddTask() {
  const el = document.getElementById('newTitle');
  const t = el.value.trim();
  if (!t) { el.focus(); return; }

  const td = {
    title: t,
    description: '',
    category: document.getElementById('newCategory').value,
    priority: document.getElementById('newPriority').value,
    status: 'todo',
    deadline: document.getElementById('newDeadline').value || null,
    checklist: [],
    notes: '',
    tables: [],
    attachments: [],
  };

  const btn = document.getElementById('addBtn');
  btn.disabled = true;
  btn.textContent = '...';
  setSyncing(true);

  try {
    const nt = await API.post('/api/tasks', td);
    tasks.unshift(nt);
    renderUI();
    el.value = '';
    document.getElementById('newDeadline').value = '';
    showToast('Đã thêm: ' + nt.title, 'success');
  } catch (e) {
    showToast('Lỗi: ' + e.message, 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = '+ Thêm';
    setSyncing(false);
  }
}

document.getElementById('newTitle').addEventListener('keydown', e => {
  if (e.key === 'Enter') handleAddTask();
});

// ============================================
// TOGGLE DONE
// ============================================
function toggleDone(id) {
  const t = findTask(id);
  if (!t) return;
  t.status = t.status === 'done' ? 'todo' : 'done';
  renderUI();
  syncTask(t);
}

async function syncTask(t) {
  setSyncing(true);
  try {
    await API.put('/api/tasks/' + t.id, t);
  } catch (e) {
    showToast('Lỗi đồng bộ', 'error');
  } finally {
    setSyncing(false);
  }
}

// ============================================
// DETAIL MODAL
// ============================================
function openDetail(id) {
  const t = findTask(id);
  if (!t) return;
  detailTaskId = id;

  const titleEl = document.getElementById('detailTitle');
  titleEl.textContent = t.title;
  titleEl.className = 'detail-title' + (t.status === 'done' ? ' done-title' : '');

  const checkEl = document.getElementById('detailCheck');
  checkEl.className = 'detail-check' + (t.status === 'done' ? ' checked' : '');

  const crEl = document.getElementById('detailCreated');
  if (t.createdAt) {
    const d = new Date(t.createdAt);
    crEl.textContent = 'Tạo lúc ' + d.toLocaleDateString('vi-VN', {
      day: '2-digit', month: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  } else {
    crEl.textContent = '';
  }

  const sL = { todo: 'Cần làm', doing: 'Đang làm', done: 'Hoàn thành' };
  const pL = { high: 'Ưu tiên cao', medium: 'Ưu tiên TB', low: 'Ưu tiên thấp' };
  const pc = t.priority === 'high' ? 'badge-high' : (t.priority === 'medium' ? 'badge-medium' : 'badge-low');
  document.getElementById('detailBadges').innerHTML =
    `<span class="detail-badge detail-badge-status status-${t.status}">${sL[t.status]}</span>` +
    `<span class="detail-badge ${pc}">${pL[t.priority]}</span>` +
    `<span class="detail-badge badge-cat">${eh(t.category)}</span>`;

  const ov = isOverdue(t.deadline) && t.status !== 'done';
  let ih = `<div class="detail-info-item"><div class="detail-info-label">Danh mục</div><div class="detail-info-value">${eh(t.category)}</div></div>`;
  ih += `<div class="detail-info-item"><div class="detail-info-label">Deadline</div><div class="detail-info-value${ov ? ' overdue-text' : ''}">`;
  if (t.deadline) {
    ih += new Date(t.deadline).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    if (ov) ih += ' (quá hạn)';
  } else {
    ih += 'Chưa đặt';
  }
  ih += '</div></div>';
  document.getElementById('detailInfoGrid').innerHTML = ih;

  document.getElementById('rteEditor').innerHTML = t.notes ||
    '<p style="color:var(--text3)"><em>Nhấn vào đây để thêm ghi chú...</em></p>';

  renderChecklist(t.checklist || []);
  renderTables(t.tables || []);
  renderAttachments(t.attachments || []);
  renderDetailNotifSection(t);
  document.getElementById('detailModal').classList.add('open');
}

function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('open');
  detailTaskId = null;
}

function toggleDoneFromDetail() {
  if (!detailTaskId) return;
  toggleDone(detailTaskId);
  openDetail(detailTaskId);
}

async function deleteFromDetail() {
  if (!detailTaskId || !confirm('Xóa công việc này?')) return;
  const id = detailTaskId;
  tasks = tasks.filter(t => String(t.id) !== String(id));
  closeDetailModal();
  renderUI();
  setSyncing(true);
  try {
    await API.delete('/api/tasks/' + id);
    showToast('Đã xóa', 'success');
  } catch (e) {
    showToast('Lỗi: ' + e.message, 'error');
  } finally {
    setSyncing(false);
  }
}

async function saveDetailAndClose() {
  if (!detailTaskId) return;
  const t = findTask(detailTaskId);
  if (!t) return;

  t.notes = document.getElementById('rteEditor').innerHTML;
  t.checklist = collectChecklist();
  t.tables = collectTables();

  closeDetailModal();
  renderUI();
  setSyncing(true);
  try {
    await API.put('/api/tasks/' + t.id, t);
    showToast('Đã lưu', 'success');
  } catch (e) {
    showToast('Lỗi: ' + e.message, 'error');
  } finally {
    setSyncing(false);
  }
}

document.getElementById('detailModal').addEventListener('click', function(e) {
  if (e.target === this) saveDetailAndClose();
});

// ============================================
// RICH TEXT EDITOR
// ============================================
function rteCmd(cmd, val) {
  document.execCommand(cmd, false, val || null);
  document.getElementById('rteEditor').focus();
}

// ============================================
// CHECKLIST
// ============================================
function renderChecklist(items) {
  const c = document.getElementById('checklistContainer');
  let h = '';
  items.forEach((item, i) => {
    h += `<div class="checklist-item" data-idx="${i}">`;
    h += `<div class="cl-check${item.done ? ' checked' : ''}" onclick="toggleClItem(${i})"></div>`;
    h += `<input class="cl-text${item.done ? ' done' : ''}" value="${ea(item.text)}" onchange="updateClText(${i},this.value)">`;
    h += `<button class="cl-del" onclick="removeClItem(${i})">&times;</button></div>`;
  });
  h += `<div class="cl-add" onclick="addClItem()">+ Thêm mục</div>`;
  c.innerHTML = h;
  updateClProgress(items);
}

function addClItem() {
  const t = findTask(detailTaskId);
  if (!t) return;
  if (!t.checklist) t.checklist = [];
  t.checklist.push({ text: '', done: false });
  renderChecklist(t.checklist);
  const inputs = document.querySelectorAll('#checklistContainer .cl-text');
  if (inputs.length) inputs[inputs.length - 1].focus();
}

function toggleClItem(i) {
  const t = findTask(detailTaskId);
  if (!t || !t.checklist[i]) return;
  t.checklist[i].done = !t.checklist[i].done;
  renderChecklist(t.checklist);
}

function updateClText(i, v) {
  const t = findTask(detailTaskId);
  if (!t || !t.checklist[i]) return;
  t.checklist[i].text = v;
}

function removeClItem(i) {
  const t = findTask(detailTaskId);
  if (!t) return;
  t.checklist.splice(i, 1);
  renderChecklist(t.checklist);
}

function collectChecklist() {
  const items = [];
  document.querySelectorAll('#checklistContainer .checklist-item').forEach(el => {
    items.push({
      text: el.querySelector('.cl-text').value,
      done: el.querySelector('.cl-check').classList.contains('checked'),
    });
  });
  return items;
}

function updateClProgress(items) {
  const total = items.length;
  const done = items.filter(i => i.done).length;
  const pct = total > 0 ? Math.round(done / total * 100) : 0;
  document.getElementById('clProgress').style.width = pct + '%';
}

// ============================================
// TABLE BUILDER
// ============================================
function renderTables(tables) {
  const c = document.getElementById('tablesContainer');
  let h = '';
  tables.forEach((tbl, ti) => {
    h += `<div class="tbl-container" data-tidx="${ti}" style="margin-bottom:16px">`;
    h += `<table class="tbl-table"><thead><tr>`;
    tbl.headers.forEach((header, ci) => {
      h += `<th><input class="tbl-cell-input" value="${ea(header)}" onchange="updateTblHeader(${ti},${ci},this.value)" style="font-weight:600"></th>`;
    });
    h += `</tr></thead><tbody>`;
    tbl.rows.forEach((row, ri) => {
      h += `<tr>`;
      row.forEach((cell, rci) => {
        h += `<td><input class="tbl-cell-input" value="${ea(cell)}" onchange="updateTblCell(${ti},${ri},${rci},this.value)"></td>`;
      });
      h += `</tr>`;
    });
    h += `</tbody></table>`;
    h += `<div class="tbl-actions">
      <button class="btn-secondary btn-sm" onclick="addTblRow(${ti})">+ Hàng</button>
      <button class="btn-secondary btn-sm" onclick="addTblCol(${ti})">+ Cột</button>
      <button class="btn-secondary btn-sm" onclick="removeTblLastRow(${ti})">- Hàng</button>
      <button class="btn-secondary btn-sm" onclick="removeTblLastCol(${ti})">- Cột</button>
      <button class="btn-danger btn-sm" onclick="removeTable(${ti})">Xóa bảng</button>
    </div></div>`;
  });
  c.innerHTML = h;
}

function addTable() {
  const t = findTask(detailTaskId);
  if (!t) return;
  if (!t.tables) t.tables = [];
  t.tables.push({ headers: ['Cột 1', 'Cột 2', 'Cột 3'], rows: [['', '', '']] });
  renderTables(t.tables);
}

function addTblRow(ti) { const t = findTask(detailTaskId); if (!t || !t.tables[ti]) return; t.tables[ti].rows.push(new Array(t.tables[ti].headers.length).fill('')); renderTables(t.tables); }
function addTblCol(ti) { const t = findTask(detailTaskId); if (!t || !t.tables[ti]) return; t.tables[ti].headers.push('Cột ' + (t.tables[ti].headers.length + 1)); t.tables[ti].rows.forEach(r => r.push('')); renderTables(t.tables); }
function removeTblLastRow(ti) { const t = findTask(detailTaskId); if (!t || !t.tables[ti] || t.tables[ti].rows.length <= 1) return; t.tables[ti].rows.pop(); renderTables(t.tables); }
function removeTblLastCol(ti) { const t = findTask(detailTaskId); if (!t || !t.tables[ti] || t.tables[ti].headers.length <= 1) return; t.tables[ti].headers.pop(); t.tables[ti].rows.forEach(r => r.pop()); renderTables(t.tables); }
function removeTable(ti) { const t = findTask(detailTaskId); if (!t) return; t.tables.splice(ti, 1); renderTables(t.tables); }
function updateTblHeader(ti, ci, v) { const t = findTask(detailTaskId); if (t && t.tables[ti]) t.tables[ti].headers[ci] = v; }
function updateTblCell(ti, ri, ci, v) { const t = findTask(detailTaskId); if (t && t.tables[ti] && t.tables[ti].rows[ri]) t.tables[ti].rows[ri][ci] = v; }
function collectTables() { const t = findTask(detailTaskId); return t ? t.tables || [] : []; }

// ============================================
// ATTACHMENTS
// ============================================
function renderAttachments(items) {
  const c = document.getElementById('attList');
  let h = '';
  items.forEach((a, i) => {
    const icon = a.type === 'file' ? '&#128196;' : '&#128279;';
    h += `<div class="att-item">
      <div class="att-icon">${icon}</div>
      <div class="att-info">
        <div class="att-name"><a href="${ea(a.url)}" target="_blank">${eh(a.name || a.url)}</a></div>
        <div class="att-meta">${a.type === 'file' ? 'File upload' : 'Link'}</div>
      </div>
      <button class="att-del" onclick="removeAttachment(${i})">&times;</button>
    </div>`;
  });
  c.innerHTML = h;
}

function addLinkAttachment() {
  const inp = document.getElementById('attLinkInput');
  const url = inp.value.trim();
  if (!url) return;
  const t = findTask(detailTaskId);
  if (!t) return;
  if (!t.attachments) t.attachments = [];
  let name = url;
  try { name = new URL(url).hostname + '/...'; } catch (e) {}
  t.attachments.push({ type: 'link', name, url });
  inp.value = '';
  renderAttachments(t.attachments);
}

async function handleFileUpload(e) {
  const file = e.target.files[0];
  if (!file) return;
  const t = findTask(detailTaskId);
  if (!t) return;
  document.getElementById('attUploading').style.display = 'block';

  const formData = new FormData();
  formData.append('file', file);

  try {
    const res = await fetch('/api/upload', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json',
      },
      body: formData,
    });
    if (!res.ok) throw new Error('Upload thất bại');
    const result = await res.json();
    if (!t.attachments) t.attachments = [];
    t.attachments.push({
      type: 'file',
      name: result.name,
      url: result.url,
      path: result.path,
      mimeType: result.mimeType,
    });
    renderAttachments(t.attachments);
    showToast('Đã upload: ' + result.name, 'success');
  } catch (err) {
    showToast('Lỗi upload: ' + err.message, 'error');
  } finally {
    document.getElementById('attUploading').style.display = 'none';
    e.target.value = '';
  }
}

function removeAttachment(i) {
  const t = findTask(detailTaskId);
  if (!t || !t.attachments) return;
  t.attachments.splice(i, 1);
  renderAttachments(t.attachments);
}

// ============================================
// EDIT MODAL (quick)
// ============================================
function openEdit(id) {
  const t = findTask(id);
  if (!t) return;
  document.getElementById('editId').value = t.id;
  document.getElementById('editTitle').value = t.title;
  document.getElementById('editDesc').value = t.description || '';
  document.getElementById('editCategory').innerHTML = categories.map(c =>
    `<option value="${ea(c)}"${c === t.category ? ' selected' : ''}>${eh(c)}</option>`
  ).join('');
  document.getElementById('editPriority').value = t.priority;
  document.getElementById('editStatus').value = t.status;
  document.getElementById('editDeadline').value = t.deadline || '';
  document.getElementById('editModal').classList.add('open');
}

function closeModal() {
  document.getElementById('editModal').classList.remove('open');
}

async function handleSaveEdit() {
  const id = document.getElementById('editId').value;
  const t = findTask(id);
  if (!t) return;

  t.title = document.getElementById('editTitle').value.trim() || t.title;
  t.description = document.getElementById('editDesc').value.trim();
  t.category = document.getElementById('editCategory').value;
  t.priority = document.getElementById('editPriority').value;
  t.status = document.getElementById('editStatus').value;
  t.deadline = document.getElementById('editDeadline').value || null;

  closeModal();
  renderUI();
  await syncTask(t);
  showToast('Đã cập nhật', 'success');
}

document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// ============================================
// DRAG & DROP (Kanban)
// ============================================
let draggedId = null;

function onDragStart(e, id) {
  draggedId = id;
  e.dataTransfer.effectAllowed = 'move';
  setTimeout(() => e.target.classList.add('dragging'), 0);
}
function onDragEnd(e) {
  e.target.classList.remove('dragging');
  document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));
  draggedId = null;
}
function onDragOver(e) { e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function onDragLeave(e) { e.currentTarget.classList.remove('drag-over'); }
function onDrop(e, status) {
  e.preventDefault();
  e.currentTarget.classList.remove('drag-over');
  if (draggedId === null) return;
  const t = findTask(draggedId);
  if (t && t.status !== status) {
    t.status = status;
    renderUI();
    syncTask(t);
  }
}

// ============================================
// HELPERS
// ============================================
function findTask(id) {
  return tasks.find(t => String(t.id) === String(id)) || null;
}
function eh(s) {
  const d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}
function ea(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/'/g,'&#39;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function isOverdue(dl) {
  if (!dl) return false;
  const d = new Date(dl), t = new Date();
  t.setHours(0, 0, 0, 0);
  return d < t;
}
function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
}
function getFiltered() {
  const s = document.getElementById('searchInput').value.toLowerCase();
  return tasks.filter(t => {
    if (activeCategory !== 'all' && t.category !== activeCategory) return false;
    if (activePriority !== 'all' && t.priority !== activePriority) return false;
    if (s) {
      const inTitle = (t.title || '').toLowerCase().includes(s);
      const inDesc = (t.description || '').toLowerCase().includes(s);
      if (!inTitle && !inDesc) return false;
    }
    return true;
  });
}

// ============================================
// RENDER TASK CARD
// ============================================
function taskCardHTML(t) {
  const pc = t.priority === 'high' ? 'badge-high' : (t.priority === 'medium' ? 'badge-medium' : 'badge-low');
  const pl = t.priority === 'high' ? 'Cao' : (t.priority === 'medium' ? 'TB' : 'Thấp');
  const ov = isOverdue(t.deadline) && t.status !== 'done';
  const clCount = (t.checklist || []).length;
  const clDone = (t.checklist || []).filter(i => i.done).length;
  const attCount = (t.attachments || []).length;
  const tblCount = (t.tables || []).length;

  return `<div class="task-card${t.status === 'done' ? ' done' : ''}"
    draggable="true"
    ondragstart="onDragStart(event,'${t.id}')"
    ondragend="onDragEnd(event)">
    <div class="task-card-actions">
      <button onclick="event.stopPropagation();openDetail('${t.id}')" title="Chi tiết">&#9776;</button>
      <button onclick="event.stopPropagation();openEdit('${t.id}')" title="Sửa nhanh">&#9998;</button>
    </div>
    <div class="task-card-top">
      <div class="task-card-check" onclick="event.stopPropagation();toggleDone('${t.id}')"></div>
      <div class="task-card-body" onclick="openDetail('${t.id}')">
        <div class="task-card-title">${eh(t.title)}</div>
        ${t.description ? `<div class="task-card-desc">${eh(t.description)}</div>` : ''}
        <div class="task-card-meta">
          <span class="badge badge-cat">${eh(t.category)}</span>
          <span class="badge ${pc}">${pl}</span>
          ${t.deadline ? `<span class="badge-date${ov ? ' overdue' : ''}">&#128197; ${formatDate(t.deadline)}${ov ? ' (quá hạn)' : ''}</span>` : ''}
          ${clCount > 0 ? `<span class="badge-extra">&#9745; ${clDone}/${clCount}</span>` : ''}
          ${attCount > 0 ? `<span class="badge-extra">&#128206; ${attCount}</span>` : ''}
          ${tblCount > 0 ? `<span class="badge-extra">&#9638; ${tblCount}</span>` : ''}
        </div>
      </div>
    </div>
  </div>`;
}

// ============================================
// RENDER STATS / KANBAN / LIST
// ============================================
function renderStats() {
  let total = tasks.length, done = 0, doing = 0, overdue = 0;
  tasks.forEach(t => {
    if (t.status === 'done') done++;
    if (t.status === 'doing') doing++;
    if (isOverdue(t.deadline) && t.status !== 'done') overdue++;
  });
  const pct = total > 0 ? Math.round(done / total * 100) : 0;
  document.getElementById('statsBar').innerHTML =
    `<div class="stat-card"><div class="stat-value">${total}</div><div class="stat-label">Tổng</div><div class="stat-bar"><div class="stat-bar-fill" style="width:100%;background:var(--accent)"></div></div></div>` +
    `<div class="stat-card"><div class="stat-value">${doing}</div><div class="stat-label">Đang làm</div><div class="stat-bar"><div class="stat-bar-fill" style="width:${total ? doing/total*100 : 0}%;background:var(--blue)"></div></div></div>` +
    `<div class="stat-card"><div class="stat-value">${pct}%</div><div class="stat-label">Hoàn thành</div><div class="stat-bar"><div class="stat-bar-fill" style="width:${pct}%;background:var(--green)"></div></div></div>` +
    `<div class="stat-card"><div class="stat-value">${overdue}</div><div class="stat-label">Quá hạn</div><div class="stat-bar"><div class="stat-bar-fill" style="width:${total ? overdue/total*100 : 0}%;background:var(--red)"></div></div></div>`;
}

function renderKanban(f) {
  const cols = [
    { key: 'todo', label: 'Cần làm', items: [] },
    { key: 'doing', label: 'Đang làm', items: [] },
    { key: 'done', label: 'Hoàn thành', items: [] },
  ];
  f.forEach(t => {
    const col = cols.find(c => c.key === t.status);
    if (col) col.items.push(t);
  });
  let h = '<div class="kanban">';
  cols.forEach(c => {
    h += `<div class="kanban-col" ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event,'${c.key}')">`;
    h += `<div class="kanban-col-header">${c.label}<span class="kanban-col-count">${c.items.length}</span></div>`;
    c.items.forEach(t => { h += taskCardHTML(t); });
    h += '</div>';
  });
  return h + '</div>';
}

function renderList(f) {
  if (!f.length) return '<div class="empty-state"><div class="empty-icon">&#9675;</div>Không có công việc nào</div>';
  const so = { todo: 0, doing: 1, done: 2 };
  const po = { high: 0, medium: 1, low: 2 };
  const s = [...f].sort((a, b) => {
    const d = (so[a.status] || 0) - (so[b.status] || 0);
    return d !== 0 ? d : (po[a.priority] || 0) - (po[b.priority] || 0);
  });
  return '<div class="list-view">' + s.map(t => taskCardHTML(t)).join('') + '</div>';
}

function renderUI() {
  const f = getFiltered();
  const main = document.getElementById('mainContent');
  if (currentView === 'dashboard') {
    main.innerHTML = renderDashboard();
    return;
  }
  main.innerHTML = currentView === 'kanban' ? renderKanban(f) : renderList(f);
  renderStats();
}

// ============================================
// DASHBOARD
// ============================================
function renderDashboard() {
  const total = tasks.length;
  const todo = tasks.filter(t => t.status === 'todo').length;
  const doing = tasks.filter(t => t.status === 'doing').length;
  const done = tasks.filter(t => t.status === 'done').length;
  const overdue = tasks.filter(t => isOverdue(t.deadline) && t.status !== 'done');
  const pct = total > 0 ? Math.round(done / total * 100) : 0;

  let totalCl = 0, doneCl = 0;
  tasks.forEach(t => {
    (t.checklist || []).forEach(c => { totalCl++; if (c.done) doneCl++; });
  });

  let h = '<div class="dashboard">';

  // Metric Cards
  h += `<div class="metric-cards">
    <div class="metric-card"><div class="metric-val">${total}</div><div class="metric-label">Tổng task</div></div>
    <div class="metric-card"><div class="metric-val" style="color:var(--blue)">${doing}</div><div class="metric-label">Đang làm</div></div>
    <div class="metric-card"><div class="metric-val" style="color:var(--green)">${pct}%</div><div class="metric-label">Hoàn thành</div></div>
    <div class="metric-card"><div class="metric-val" style="color:var(--red)">${overdue.length}</div><div class="metric-label">Quá hạn</div></div>
  </div>`;

  // Row 1: Status donut + Category bar
  h += '<div class="dash-row">';
  h += `<div class="dash-card"><div class="dash-card-title"><span>&#9685;</span> Phân bổ trạng thái</div>`;
  h += renderDonut([
    { label: 'Cần làm', value: todo, color: 'var(--blue)' },
    { label: 'Đang làm', value: doing, color: 'var(--orange)' },
    { label: 'Hoàn thành', value: done, color: 'var(--green)' },
  ], 120) + '</div>';

  const catData = categories.map(c => ({
    label: c,
    value: tasks.filter(t => t.category === c).length
  })).filter(d => d.value > 0);
  h += `<div class="dash-card"><div class="dash-card-title"><span>&#9638;</span> Theo danh mục</div>`;
  h += renderBarChart(catData, 'var(--accent)') + '</div>';
  h += '</div>';

  // Row 2: Priority donut + Line chart
  h += '<div class="dash-row">';
  const high = tasks.filter(t => t.priority === 'high' && t.status !== 'done').length;
  const med  = tasks.filter(t => t.priority === 'medium' && t.status !== 'done').length;
  const low  = tasks.filter(t => t.priority === 'low' && t.status !== 'done').length;
  h += `<div class="dash-card"><div class="dash-card-title"><span>&#9888;</span> Ưu tiên (chưa xong)</div>`;
  h += renderDonut([
    { label: 'Cao', value: high, color: 'var(--red)' },
    { label: 'Trung bình', value: med, color: 'var(--orange)' },
    { label: 'Thấp', value: low, color: 'var(--green)' },
  ], 120) + '</div>';
  h += `<div class="dash-card"><div class="dash-card-title"><span>&#128200;</span> Tiến độ 7 ngày qua</div>`;
  h += renderLineChart() + '</div>';
  h += '</div>';

  // Row 3: Checklist + Upcoming
  h += '<div class="dash-row">';
  h += `<div class="dash-card"><div class="dash-card-title"><span>&#9745;</span> Tổng checklist</div>`;
  h += renderDonut([
    { label: 'Đã xong', value: doneCl, color: 'var(--green)' },
    { label: 'Chưa xong', value: totalCl - doneCl, color: 'var(--border2)' },
  ], 90) + '</div>';

  const upcoming = tasks.filter(t => {
    if (!t.deadline || t.status === 'done') return false;
    const dl = new Date(t.deadline), now = new Date();
    now.setHours(0, 0, 0, 0);
    const diff = (dl - now) / (1000 * 60 * 60 * 24);
    return diff >= 0 && diff <= 7;
  }).sort((a, b) => new Date(a.deadline) - new Date(b.deadline));

  h += `<div class="dash-card"><div class="dash-card-title"><span>&#128197;</span> Sắp đến hạn (7 ngày)</div>`;
  if (upcoming.length === 0) {
    h += '<div class="dash-empty">Không có task nào sắp đến hạn</div>';
  } else {
    h += '<table class="dash-table"><thead><tr><th>Task</th><th>Deadline</th><th>Ưu tiên</th></tr></thead><tbody>';
    upcoming.forEach(t => {
      const pc = t.priority === 'high' ? 'badge-high' : (t.priority === 'medium' ? 'badge-medium' : 'badge-low');
      const pl = t.priority === 'high' ? 'Cao' : (t.priority === 'medium' ? 'TB' : 'Thấp');
      h += `<tr>
        <td class="td-title" onclick="openDetail('${t.id}')">${eh(t.title)}</td>
        <td>${formatDate(t.deadline)}</td>
        <td><span class="td-badge ${pc}">${pl}</span></td>
      </tr>`;
    });
    h += '</tbody></table>';
  }
  h += '</div>';
  h += '</div>';

  // Overdue + Timeline
  h += '<div class="dash-row">';
  h += `<div class="dash-card"><div class="dash-card-title"><span style="color:var(--red)">&#9888;</span> Task quá hạn</div>`;
  if (overdue.length === 0) {
    h += '<div class="dash-empty">Không có task quá hạn &#127881;</div>';
  } else {
    h += '<table class="dash-table"><thead><tr><th>Task</th><th>Deadline</th><th>Danh mục</th><th>Ưu tiên</th></tr></thead><tbody>';
    [...overdue].sort((a, b) => new Date(a.deadline) - new Date(b.deadline)).forEach(t => {
      const pc = t.priority === 'high' ? 'badge-high' : (t.priority === 'medium' ? 'badge-medium' : 'badge-low');
      const pl = t.priority === 'high' ? 'Cao' : (t.priority === 'medium' ? 'TB' : 'Thấp');
      const days = Math.ceil((new Date() - new Date(t.deadline)) / (1000 * 60 * 60 * 24));
      h += `<tr>
        <td class="td-title" onclick="openDetail('${t.id}')">${eh(t.title)}</td>
        <td class="td-overdue">${formatDate(t.deadline)} (-${days} ngày)</td>
        <td>${eh(t.category)}</td>
        <td><span class="td-badge ${pc}">${pl}</span></td>
      </tr>`;
    });
    h += '</tbody></table>';
  }
  h += '</div>';

  h += `<div class="dash-card"><div class="dash-card-title"><span>&#128336;</span> Timeline gần đây</div>`;
  h += renderTimeline() + '</div>';
  h += '</div>';

  h += '</div>';
  return h;
}

// ============================================
// CHART HELPERS
// ============================================
function renderDonut(data, size = 120) {
  const r = size / 2 - 8, cx = size / 2, cy = size / 2;
  const total = data.reduce((s, d) => s + d.value, 0);
  if (total === 0) return '<div class="dash-empty">Chưa có dữ liệu</div>';

  let h = `<div class="donut-wrap"><svg class="donut-svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">`;
  const circumference = 2 * Math.PI * r;
  let offset = 0;
  data.forEach(d => {
    if (d.value === 0) return;
    const pct = d.value / total;
    const dashLen = pct * circumference;
    const dashGap = circumference - dashLen;
    h += `<circle cx="${cx}" cy="${cy}" r="${r}" fill="none" stroke="${d.color}" stroke-width="14" stroke-dasharray="${dashLen} ${dashGap}" stroke-dashoffset="${-offset}" style="transform:rotate(-90deg);transform-origin:center"/>`;
    offset += dashLen;
  });
  h += `<text x="${cx}" y="${cy}" text-anchor="middle" dy=".1em" font-size="1.1rem" font-weight="700" fill="var(--text)">${total}</text>`;
  h += `<text x="${cx}" y="${cy + 14}" text-anchor="middle" font-size=".55rem" fill="var(--text3)">tổng</text>`;
  h += '</svg>';
  h += '<div class="donut-legend">';
  data.forEach(d => {
    const pct = Math.round(d.value / total * 100);
    h += `<div class="donut-legend-item"><span class="donut-legend-dot" style="background:${d.color}"></span>${d.label} (${pct}%)<span class="donut-legend-val">${d.value}</span></div>`;
  });
  h += '</div></div>';
  return h;
}

function renderBarChart(data, color) {
  if (!data.length) return '<div class="dash-empty">Chưa có dữ liệu</div>';
  const max = Math.max(...data.map(d => d.value), 1);
  const colors = ['var(--blue)', 'var(--green)', 'var(--orange)', 'var(--red)', 'var(--accent)', '#8b5cf6', '#06b6d4', '#ec4899'];
  let h = '<div class="bar-chart">';
  data.forEach((d, i) => {
    const pct = Math.round(d.value / max * 100);
    const c = colors[i % colors.length];
    h += `<div class="bar-group"><div class="bar-val">${d.value}</div><div class="bar-fill" style="height:${pct}%;background:${c}"></div><div class="bar-label" title="${ea(d.label)}">${eh(d.label)}</div></div>`;
  });
  return h + '</div>';
}

function renderLineChart() {
  const days = [], labels = [];
  for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    d.setHours(0, 0, 0, 0);
    days.push(d);
    labels.push(d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }));
  }

  const created = days.map(day => {
    const nextDay = new Date(day);
    nextDay.setDate(nextDay.getDate() + 1);
    return tasks.filter(t => {
      if (!t.createdAt) return false;
      const c = new Date(t.createdAt);
      return c >= day && c < nextDay;
    }).length;
  });

  const maxVal = Math.max(...created, 1);
  const w = 100, h2 = 100, padL = 8, padR = 4, padT = 8, padB = 20;
  const plotW = w - padL - padR, plotH = h2 - padT - padB;

  let svg = `<div class="line-chart-wrap"><svg class="line-chart-svg" viewBox="0 0 ${w} ${h2}">`;

  // Grid lines
  for (let g = 0; g <= 4; g++) {
    const gy = padT + plotH - plotH * (g / 4);
    svg += `<line x1="${padL}" y1="${gy}" x2="${w - padR}" y2="${gy}" class="line-chart-grid"/>`;
  }

  const pts = [];
  for (let k = 0; k < 7; k++) {
    const x = padL + k * (plotW / 6);
    const y = padT + plotH - plotH * (created[k] / maxVal);
    pts.push(`${x},${y}`);
    svg += `<text x="${x}" y="${h2 - 2}" text-anchor="middle" class="line-chart-label">${labels[k]}</text>`;
  }

  const area = `${padL},${padT + plotH} ${pts.join(' ')} ${padL + plotW},${padT + plotH}`;
  svg += `<polygon points="${area}" fill="var(--blue)" class="line-chart-area"/>`;
  svg += `<polyline points="${pts.join(' ')}" class="line-chart-path" stroke="var(--blue)"/>`;
  pts.forEach(p => {
    const [px, py] = p.split(',');
    svg += `<circle cx="${px}" cy="${py}" class="line-chart-dot" fill="var(--blue)"/>`;
  });

  svg += `</svg><div style="display:flex;gap:16px;justify-content:center;margin-top:8px">`;
  svg += `<div class="donut-legend-item"><span class="donut-legend-dot" style="background:var(--blue)"></span><span style="font-size:.75rem">Task tạo mới</span></div>`;
  svg += '</div></div>';
  return svg;
}

function renderTimeline() {
  const recent = tasks
    .filter(t => t.createdAt)
    .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
    .slice(0, 10);
  if (!recent.length) return '<div class="dash-empty">Chưa có hoạt động</div>';

  let h = '<div class="timeline">';
  recent.forEach(t => {
    let dotClass = t.status === 'done' ? 'dot-done' : (t.status === 'doing' ? 'dot-doing' : 'dot-todo');
    if (isOverdue(t.deadline) && t.status !== 'done') dotClass = 'dot-overdue';
    const sLabel = { todo: 'Cần làm', doing: 'Đang làm', done: 'Hoàn thành' }[t.status];
    const date = t.createdAt ? new Date(t.createdAt).toLocaleDateString('vi-VN', {
      day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit'
    }) : '';
    h += `<div class="timeline-item">
      <div class="timeline-dot ${dotClass}"></div>
      <div class="timeline-date">${date}</div>
      <div class="timeline-title" onclick="openDetail('${t.id}')">${eh(t.title)}</div>
      <div class="timeline-meta">${eh(t.category)} &middot; ${sLabel}</div>
    </div>`;
  });
  return h + '</div>';
}

// ============================================
// NOTIFICATION ENGINE
// ============================================
const NOTIF_OPTIONS = [
  { minutes: 30,    label: '30 phút trước',  sub: 'Nhắc khi còn 30 phút' },
  { minutes: 60,    label: '1 giờ trước',    sub: 'Nhắc khi còn 1 giờ' },
  { minutes: 180,   label: '3 giờ trước',    sub: 'Nhắc khi còn 3 giờ' },
  { minutes: 360,   label: '6 giờ trước',    sub: 'Nhắc khi còn 6 giờ' },
  { minutes: 1440,  label: '1 ngày trước',   sub: 'Nhắc vào ngày hôm trước' },
  { minutes: 2880,  label: '2 ngày trước',   sub: 'Nhắc trước 2 ngày' },
  { minutes: 4320,  label: '3 ngày trước',   sub: 'Nhắc trước 3 ngày' },
  { minutes: 10080, label: '1 tuần trước',   sub: 'Nhắc trước 1 tuần' },
];

let notifyBefore = [1440];
let notifCheckTimer = null;

function openNotifSettings() {
  renderNotifOptions();
  renderNotifPermBar();
  renderNotifPreview();
  document.getElementById('notifModal').classList.add('open');
}
function closeNotifSettings() {
  document.getElementById('notifModal').classList.remove('open');
}
document.getElementById('notifModal').addEventListener('click', function(e) {
  if (e.target === this) closeNotifSettings();
});

function renderNotifPermBar() {
  const perm = Notification.permission;
  const label = document.getElementById('notifPermLabel');
  const sub   = document.getElementById('notifPermSub');
  const btn   = document.getElementById('notifPermBtn');
  if (perm === 'granted') {
    label.textContent = 'Thông báo: Đã bật ✓';
    sub.textContent   = 'Bạn sẽ nhận được nhắc nhở từ trình duyệt';
    btn.textContent   = 'Đã cấp phép';
    btn.className     = 'notif-perm-btn notif-perm-granted';
    btn.disabled      = true;
  } else if (perm === 'denied') {
    label.textContent = 'Thông báo: Bị chặn ✗';
    sub.textContent   = 'Vào Settings trình duyệt để cho phép thông báo';
    btn.textContent   = 'Bị chặn';
    btn.className     = 'notif-perm-btn notif-perm-denied';
    btn.disabled      = true;
  } else {
    label.textContent = 'Thông báo: Chưa cấp phép';
    sub.textContent   = 'Nhấn để cho phép nhận thông báo nhắc việc';
    btn.textContent   = 'Cho phép';
    btn.className     = 'notif-perm-btn notif-perm-request';
    btn.disabled      = false;
  }
}

async function handlePermissionClick() {
  if (Notification.permission === 'default') {
    const result = await Notification.requestPermission();
    renderNotifPermBar();
    if (result === 'granted') {
      showToast('Đã bật thông báo nhắc việc!', 'success');
      scheduleNotifCheck();
    } else {
      showToast('Bạn đã từ chối thông báo', 'error');
    }
  }
}

function renderNotifOptions() {
  const container = document.getElementById('notifOptions');
  container.innerHTML = NOTIF_OPTIONS.map(opt => {
    const selected = notifyBefore.includes(opt.minutes);
    return `<div class="notif-option${selected ? ' selected' : ''}" onclick="toggleNotifOption(${opt.minutes})">
      <div class="notif-check"></div>
      <div>
        <div class="notif-option-text">${opt.label}</div>
        <div class="notif-option-sub">${opt.sub}</div>
      </div>
    </div>`;
  }).join('');
}

function toggleNotifOption(minutes) {
  const idx = notifyBefore.indexOf(minutes);
  if (idx === -1) notifyBefore.push(minutes);
  else if (notifyBefore.length > 1) notifyBefore.splice(idx, 1);
  renderNotifOptions();
  renderNotifPreview();
}

function renderNotifPreview() {
  const container = document.getElementById('notifPreview');
  const now = new Date();
  const maxBefore = Math.max(...notifyBefore);
  const upcoming = tasks
    .filter(t => t.status !== 'done' && t.deadline)
    .map(t => ({ ...t, diffMin: (new Date(t.deadline + 'T23:59:00') - now) / 60000 }))
    .filter(t => t.diffMin > -1440 && t.diffMin <= maxBefore)
    .sort((a, b) => a.diffMin - b.diffMin)
    .slice(0, 6);

  if (!upcoming.length) {
    container.innerHTML = '<div class="notif-empty-preview">Không có task nào sắp đến hạn</div>';
    return;
  }
  container.innerHTML = upcoming.map(t => {
    const d = t.diffMin;
    let dotClass, timeText;
    if (d < 0)        { dotClass = 'dot-urgent'; timeText = 'Đã quá hạn'; }
    else if (d <= 60) { dotClass = 'dot-urgent'; timeText = `Còn ${Math.round(d)} phút`; }
    else if (d <= 1440){ dotClass = 'dot-soon';  timeText = `Còn ${Math.round(d/60)} giờ`; }
    else               { dotClass = 'dot-normal'; timeText = `Còn ${Math.round(d/1440)} ngày`; }
    return `<div class="notif-preview-item">
      <div class="notif-preview-dot ${dotClass}"></div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${eh(t.title)}</div>
        <div style="font-size:.68rem;color:var(--text3)">${eh(t.category||'')} · ${timeText}</div>
      </div>
    </div>`;
  }).join('');
}

async function saveNotifSettings() {
  try {
    await API.put('/api/settings', { notifyBefore });
    updateNotifBadge();
    scheduleNotifCheck();
    closeNotifSettings();
    showToast('Đã lưu cài đặt nhắc việc', 'success');
  } catch (e) {
    showToast('Lỗi lưu cài đặt', 'error');
  }
}

function updateNotifBadge() {
  const btn = document.getElementById('notifBtn');
  const now = new Date();
  const maxBefore = Math.max(...notifyBefore);
  const count = tasks.filter(t => {
    if (t.status === 'done' || !t.deadline) return false;
    const diffMin = (new Date(t.deadline + 'T23:59:00') - now) / 60000;
    return diffMin >= 0 && diffMin <= maxBefore;
  }).length;
  const old = btn.querySelector('.notif-badge');
  if (old) old.remove();
  if (count > 0) {
    const badge = document.createElement('span');
    badge.className = 'notif-badge';
    badge.textContent = count > 9 ? '9+' : count;
    btn.appendChild(badge);
    btn.classList.add('active-notif');
  } else {
    btn.classList.remove('active-notif');
  }
}

function notifKey(taskId, minutes) {
  return `tf_notif_${taskId}_${minutes}`;
}
function hasNotified(taskId, minutes, deadline) {
  return localStorage.getItem(notifKey(taskId, minutes)) === deadline;
}
function markNotified(taskId, minutes, deadline) {
  localStorage.setItem(notifKey(taskId, minutes), deadline);
}

function checkNotifications() {
  if (Notification.permission !== 'granted') return;
  const now = new Date();
  tasks.forEach(t => {
    if (t.status === 'done' || !t.deadline) return;
    const diffMin = (new Date(t.deadline + 'T23:59:00') - now) / 60000;
    notifyBefore.forEach(minutes => {
      if (diffMin > 0 && diffMin <= minutes && diffMin > minutes - 2) {
        if (!hasNotified(t.id, minutes, t.deadline)) {
          markNotified(t.id, minutes, t.deadline);
          fireBrowserNotif(t, minutes, diffMin);
        }
      }
    });
  });
  updateNotifBadge();
}

function fireBrowserNotif(task, minutesBefore, diffMin) {
  const opt = NOTIF_OPTIONS.find(o => o.minutes === minutesBefore);
  const label = opt ? opt.label : `${minutesBefore} phút trước`;
  let timeText;
  if (diffMin <= 60)        timeText = `còn ${Math.round(diffMin)} phút`;
  else if (diffMin <= 1440) timeText = `còn ${Math.round(diffMin/60)} giờ`;
  else                      timeText = `còn ${Math.round(diffMin/1440)} ngày`;
  const em = task.priority === 'high' ? '🔴' : (task.priority === 'medium' ? '🟡' : '🟢');
  const n = new Notification(`${em} Nhắc việc: ${task.title}`, {
    body: `📅 Deadline ${new Date(task.deadline).toLocaleDateString('vi-VN')} – ${timeText}\n📁 ${task.category || 'Không có danh mục'}\n⏰ ${label}`,
    icon: '/favicon.ico',
    tag: `taskflow-${task.id}-${minutesBefore}`,
    requireInteraction: task.priority === 'high',
  });
  n.onclick = () => { window.focus(); openDetail(task.id); n.close(); };
}

function renderDetailNotifSection(task) {
  const container = document.getElementById('detailNotifContent');
  if (!container) return;
  const perm = ('Notification' in window) ? Notification.permission : 'unsupported';
  let html = '';

  // Permission hint
  if (perm === 'default') {
    html += `<div class="detail-notif-perm-hint">&#9888; Chưa bật thông báo &ndash; <button class="btn-link-inline" onclick="handlePermissionClick()">Bật ngay</button></div>`;
  } else if (perm === 'denied') {
    html += `<div class="detail-notif-perm-hint">&#10060; Thông báo bị chặn trong trình duyệt</div>`;
  } else if (perm === 'unsupported') {
    html += `<div class="detail-notif-perm-hint">&#10060; Trình duyệt không hỗ trợ thông báo</div>`;
  }

  // Time chips
  html += `<div class="detail-notif-chips">`;
  NOTIF_OPTIONS.forEach(opt => {
    const active = notifyBefore.includes(opt.minutes);
    html += `<span class="detail-notif-chip${active ? ' active' : ''}" onclick="toggleDetailNotifChip(${opt.minutes})">${opt.label}</span>`;
  });
  html += `</div>`;

  // Task-specific status
  if (!task.deadline) {
    html += `<div class="detail-notif-status-bar">&#128337; Task chưa có deadline &ndash; chưa thể nhắc</div>`;
  } else if (task.status === 'done') {
    html += `<div class="detail-notif-status-bar status-done">&#9989; Task đã hoàn thành &ndash; không nhắc nữa</div>`;
  } else {
    const now = new Date();
    const diffMin = (new Date(task.deadline + 'T23:59:00') - now) / 60000;
    if (diffMin < 0) {
      html += `<div class="detail-notif-status-bar status-overdue">&#128308; Task đã quá hạn</div>`;
    } else {
      const upcoming = notifyBefore.filter(m => diffMin <= m).sort((a, b) => a - b);
      if (!upcoming.length) {
        html += `<div class="detail-notif-status-bar">&#128276; Deadline còn xa &ndash; chưa đến thời điểm nhắc nào</div>`;
      } else {
        const labels = upcoming.map(m => (NOTIF_OPTIONS.find(x => x.minutes === m) || {label: m+'ph'}).label);
        html += `<div class="detail-notif-status-bar status-active">&#128276; Sẽ nhắc: ${labels.join(' · ')}</div>`;
      }
    }
  }

  container.innerHTML = html;
}

async function toggleDetailNotifChip(minutes) {
  const idx = notifyBefore.indexOf(minutes);
  if (idx >= 0) {
    if (notifyBefore.length > 1) notifyBefore.splice(idx, 1);
    else { showToast('Cần chọn ít nhất 1 mốc nhắc', 'error'); return; }
  } else {
    notifyBefore.push(minutes);
  }
  try {
    await API.put('/api/settings', { notifyBefore });
  } catch (e) { /* silent */ }
  updateNotifBadge();
  if (Notification.permission === 'granted') scheduleNotifCheck();
  const t = findTask(detailTaskId);
  if (t) renderDetailNotifSection(t);
}

function scheduleNotifCheck() {
  if (notifCheckTimer) clearInterval(notifCheckTimer);
  checkNotifications();
  notifCheckTimer = setInterval(checkNotifications, 60 * 1000);
}

// ============================================
// KEYBOARD SHORTCUTS
// ============================================
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeDetailModal(); closeModal(); closeNotifSettings(); }
  if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
    const tag = document.activeElement.tagName;
    if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT' && !document.activeElement.isContentEditable) {
      e.preventDefault();
      document.getElementById('searchInput').focus();
    }
  }
});

// ============================================
// KHỞI ĐỘNG
// ============================================
loadData();
</script>
</body>
</html>
