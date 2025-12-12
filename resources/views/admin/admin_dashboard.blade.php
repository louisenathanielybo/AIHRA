@php
use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlHRA Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    :root {
        --primary: #2d5a3d;
        --secondary: #4a8c5e;
        --accent: #3a7d54;
        --light: #f0f7f2;
        --dark: #1e3b2a;
        --success: #4caf50;
        --warning: #ff9800;
        --danger: #f44336;
        --gray: #789984;
        --sidebar-width: clamp(200px, 20vw, 250px);
        --card-bg: #ffffff;
        --hover-light: #e8f5e8;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f8fdf9;
        color: #2d5a3d;
        display: flex;
        min-height: 100vh;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, #0d3d2d 0%, #1a4a35 100%);
        color: white;
        height: 100vh;
        position: fixed;
        padding: 20px 0;
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(10, 47, 45, 0.1);
    }

    .sidebar-header {
        padding: 0 20px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 20px;
    }

    .sidebar-header h2 {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #e8f5e8;
    }

    .sidebar-menu {
        list-style: none;
    }

    .sidebar-menu li {
        margin-bottom: 5px;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #e8f5e8;
        text-decoration: none;
        transition: all 0.3s;
        border-radius: 0 8px 8px 0;
        margin-right: 10px;
    }

    .sidebar-menu a:hover, .sidebar-menu a.active {
        background: rgba(255,255,255,0.15);
        border-left: 4px solid #1A6B61;
        color: white;
    }

    .sidebar-menu i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
        color: #a8d5b5;
    }

    .sidebar-footer {
        position: absolute;
        bottom: 20px;
        width: 100%;
        padding: 0 20px;
    }
    
    .sidebar-footer a[data-section="account-settings"]:hover {
        background: rgba(255,255,255,0.2) !important;
        border-color: rgba(255,255,255,0.3) !important;
        transform: translateX(2px);
    }
    
    .sidebar-footer a[data-section="account-settings"].active {
        background: rgba(255,255,255,0.25) !important;
        border-color: var(--secondary) !important;
    }

    .account-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    .account-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        border: 2px solid #a8d5b5;
        overflow: hidden;
        position: relative;
    }
    
    .account-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .account-avatar-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .account-details {
        flex: 1;
    }

    .account-name {
        font-weight: 500;
        font-size: 0.9rem;
        color: white;
    }

    .account-role {
        font-size: 0.8rem;
        color: #a8d5b5;
    }

    .logout-btn {
        background: none;
        border: none;
        color: #a8d5b5;
        cursor: pointer;
        padding: 5px;
        border-radius: 3px;
        transition: all 0.3s;
    }

    .logout-btn:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        padding: clamp(15px, 2vw, 20px);
        background: #f8fdf9;
        max-width: 100vw;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0efe5;
    }

    .header h1 {
        color: var(--primary);
        font-size: 1.8rem;
        font-weight: 600;
    }

    .header-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .date-range {
        background: white;
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);
        font-size: 0.9rem;
        color: var(--primary);
        border: 1px solid #e0efe5;
    }

    .date-range-filter {
        display: flex;
        align-items: center;
        background: white;
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);
        border: 1px solid #e0efe5;
    }

    #dateRangeSelect {
        outline: none;
        transition: all 0.3s;
    }

    #dateRangeSelect:hover {
        border-color: var(--secondary);
    }

    #dateRangeSelect:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(74, 140, 94, 0.1);
    }

    .user-account {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);
        cursor: pointer;
        border: 1px solid #e0efe5;
        transition: all 0.3s;
    }

    .user-account:hover {
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.12);
        transform: translateY(-1px);
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        border: 2px solid #a8d5b5;
        overflow: hidden;
        position: relative;
    }
    
    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .user-avatar-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* ===== DASHBOARD CARDS ===== */
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        border: 1px solid #e0efe5;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(45, 90, 61, 0.12);
    }

    .stat-card {
        text-align: center;
    }

    .stat-card h3 {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 10px;
        font-weight: 500;
    }

    .stat-card .value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary);
        margin: 10px 0;
    }

    .stat-card .trend {
        font-size: 0.8rem;
        margin-top: 5px;
        font-weight: 500;
    }

    .trend.up { color: var(--success); }
    .trend.down { color: var(--danger); }

    /* ===== TAB SYSTEM ===== */
    .section-content {
        display: none;
    }

    .section-content.active {
        display: block;
    }

    /* Nested Dashboard Tabs */
    .dashboard-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #e0efe5;
    }

    .dashboard-tab-btn {
        padding: 12px 24px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        background: none;
        border: none;
        font-size: 1rem;
        color: var(--gray);
        transition: all 0.3s;
        font-weight: 500;
    }

    .dashboard-tab-btn.active {
        border-bottom: 3px solid var(--secondary);
        color: var(--primary);
        font-weight: 600;
    }

    .dashboard-tab-btn:hover:not(.active) {
        color: var(--primary);
        background: var(--hover-light);
    }

    .dashboard-tab-content {
        display: none;
    }

    .dashboard-tab-content.active {
        display: block;
    }

    /* ===== TABLES ===== */
    .data-table {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        margin-bottom: 30px;
        overflow-x: auto;
        border: 1px solid #e0efe5;
    }

    .data-table h3 {
        margin-bottom: 15px;
        color: var(--primary);
        font-weight: 600;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e8f5e8;
    }

    th {
        background: var(--light);
        font-weight: 600;
        color: var(--primary);
        border-bottom: 2px solid var(--secondary);
    }

    tr:hover {
        background: var(--hover-light);
    }

    /* ===== CHARTS ===== */
    .chart-container {
        margin-bottom: 30px;
    }

    .chart-container h3 {
        margin-bottom: 15px;
        color: var(--primary);
        font-weight: 600;
    }

    .chart-box {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        height: 300px;
        border: 1px solid #e0efe5;
    }

    /* ===== TOPICS ===== */
    .topics-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        margin-bottom: 30px;
        border: 1px solid #e0efe5;
    }

    .topics-container h3 {
        margin-bottom: 15px;
        color: var(--primary);
        font-weight: 600;
    }

    .topic-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e8f5e8;
        transition: all 0.3s;
    }

    .topic-item:hover {
        background: var(--hover-light);
        margin: 0 -10px;
        padding: 12px 10px;
        border-radius: 6px;
    }

    .topic-item:last-child {
        border-bottom: none;
    }

    .topic-name {
        font-weight: 500;
        color: var(--primary);
    }

    .topic-count {
        background: var(--light);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        color: var(--primary);
        font-weight: 500;
    }

    /* ===== STATUS BADGES ===== */
    .status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status.normal { background: #e8f5e8; color: var(--success); }
    .status.flagged { background: #ffebee; color: var(--danger); }
    .status.open { background: #e8f5e8; color: var(--success); }
    .status.reserved { background: #fff3e0; color: var(--warning); }

    /* ===== BUTTONS ===== */
    .btn {
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-primary {
        background: var(--secondary);
        color: white;
        box-shadow: 0 2px 6px rgba(74, 140, 94, 0.3);
    }

    .btn-secondary {
        background: var(--light);
        color: var(--primary);
        border: 1px solid #c8e6c9;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
        box-shadow: 0 2px 6px rgba(244, 67, 54, 0.3);
    }

    .btn:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.2);
    }

    /* ===== TICKETS STYLES ===== */
    .tickets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-box {
        position: relative;
        width: 300px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 15px 10px 40px;
        border: 1px solid #e0efe5;
        border-radius: 20px;
        font-size: 0.9rem;
        background: white;
        transition: all 0.3s;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(74, 140, 94, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
    }

    .filters {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-group label {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--primary);
    }

    .filter-group select {
        padding: 8px 12px;
        border: 1px solid #e0efe5;
        border-radius: 8px;
        font-size: 0.9rem;
        background: white;
        transition: all 0.3s;
    }

    .filter-group select:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(74, 140, 94, 0.1);
    }

    .priority {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .priority.high { background: #ffebee; color: var(--danger); }
    .priority.medium { background: #fff3e0; color: var(--warning); }
    .priority.low { background: #e8f5e8; color: var(--success); }
    .priority.urgent { background: #fbe9e7; color: #b00020; border: 1px solid #ffcdd2; font-weight: 600; }

    .status.open { background: #e8f5e8; color: var(--success); }
    .status.resolved { background: #e3f2fd; color: var(--secondary); }

    /* ===== ACCOUNT MANAGEMENT ===== */
    .account-management-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .quick-action-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #e0efe5;
    }

    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(45, 90, 61, 0.12);
        background: var(--hover-light);
    }

    .quick-action-icon {
        font-size: 1.8rem;
        margin-bottom: 10px;
        display: block;
        color: var(--secondary);
    }

    .quick-action-text {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--primary);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .enhanced-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        border: 1px solid #e0efe5;
    }

    .enhanced-table th, .enhanced-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e8f5e8;
    }

    .enhanced-table th {
        background: var(--light);
        font-weight: 600;
        color: var(--primary);
        border-bottom: 2px solid var(--secondary);
    }

    /* Sortable table headers */
    .sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 25px !important;
    }

    .sortable:hover {
        background: #e0efe5;
    }

    .sortable::after {
        content: '△';
        position: absolute;
        right: 8px;
        opacity: 0.3;
        font-size: 0.8rem;
    }

    .sortable.asc::after {
        content: '△';
        opacity: 1;
    }

    .sortable.desc::after {
        content: '▽';
        opacity: 1;
    }

    .role-badge, .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .role-admin { background: #e3f2fd; color: var(--secondary); }
    .role-employee { background: #e8f5e8; color: var(--success); }
    .role-hr { background: #fff3e0; color: var(--warning); }
    .role-manager { background: #f3e5f5; color: #7b1fa2; }

    .status-active { background: #e8f5e8; color: var(--success); }
    .status-inactive { background: #f5f5f5; color: var(--gray); }
    .status-suspended { background: #ffebee; color: var(--danger); }

    .action-buttons-cell {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-view { background: #e3f2fd; color: var(--secondary); }
    .btn-edit { background: #fff3e0; color: var(--warning); }
    .btn-reset { background: #e8f5e8; color: var(--success); }
    .btn-delete { background: #ffebee; color: var(--danger); }

    .btn-action:hover {
        opacity: 0.8;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(45, 90, 61, 0.2);
    }

    /* ===== MODALS ===== */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(45, 90, 61, 0.5);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(45, 90, 61, 0.2);
        border: 1px solid #e0efe5;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e8f5e8;
        background: var(--light);
        border-radius: 12px 12px 0 0;
    }

    .modal-header h3 {
        color: var(--primary);
        margin: 0;
        font-weight: 600;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--gray);
        transition: all 0.3s;
    }

    .close-modal:hover {
        color: var(--primary);
    }

    .modal-body {
        padding: 14px;
    }

    /* ===== TICKET MODAL ===== */
    .ticket-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .ticket-detail-item {
        margin-bottom: 15px;
    }

    .ticket-detail-item label {
        font-weight: 600;
        color: var(--primary);
        display: block;
        margin-bottom: 5px;
    }

    .ticket-detail-item .value {
        padding: 10px 12px;
        background: var(--light);
        border-radius: 6px;
        border: 1px solid #e0efe5;
        color: var(--dark);
    }

    .ticket-message {
        grid-column: 1 / -1;
    }

    .ticket-message .value {
        min-height: 100px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    /* ===== FORMS ===== */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .form-group {
        margin-bottom: 10px;
    }

    .form-group-full {
        grid-column: 1 / -1;
        margin-bottom: 10px;
    }

    .form-group label {
        display: block;
        margin-bottom: 4px;
        font-weight: 500;
        color: var(--primary);
    }

    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #e0efe5;
        border-radius: 6px;
        font-size: 0.9rem;
        background: white;
        transition: all 0.3s;
    }

    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(74, 140, 94, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 60px;
    }

    /* ===== PAGINATION ===== */
    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding: 15px 0;
    }

    .pagination-btn {
        padding: 8px 15px;
        border: 1px solid #e0efe5;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        color: var(--primary);
        font-weight: 500;
    }

    .pagination-btn:hover:not(:disabled) {
        background: var(--hover-light);
        border-color: var(--secondary);
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        font-size: 0.9rem;
        color: var(--gray);
    }

    .pagination-pages {
        display: flex;
        gap: 5px;
    }

    .page-number {
        padding: 8px 12px;
        border: 1px solid #e0efe5;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        color: var(--primary);
        font-weight: 500;
    }

    .page-number.active {
        background: var(--secondary);
        color: white;
        border-color: var(--secondary);
    }

    .page-number:hover:not(.active) {
        background: var(--hover-light);
        border-color: var(--secondary);
    }

    .pagination-ellipsis {
        padding: 8px 5px;
        color: var(--gray);
    }

    /* Laravel pagination SVG icon size override */
    nav[role="navigation"] svg {
        width: 14px !important;
        height: 14px !important;
    }

    /* Center pagination */
    nav[role="navigation"] {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* ===== FEEDBACK ===== */
    .feedback-section {
        margin-bottom: 30px;
    }

    .feedback-section h2 {
        color: var(--primary);
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e8f5e8;
        font-weight: 600;
    }

    .success-message {
        background: #e8f5e8;
        color: var(--success);
        padding: 12px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        border: 1px solid #c8e6c9;
        font-weight: 500;
    }

    .profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--secondary);
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.2);
    }

    /* ===== MOBILE RESPONSIVENESS ===== */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primary);
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: all 0.3s;
    }

    .mobile-menu-btn:hover {
        background: var(--hover-light);
    }

    @media (max-width: 1200px) {
        .dashboard-cards, .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .mobile-menu-btn {
            display: block;
        }
        
        .dashboard-cards, .quick-actions {
            grid-template-columns: 1fr;
        }
        
        .header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .header-info {
            width: 100%;
            justify-content: space-between;
        }
        
        .account-management-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .search-box {
            width: 100%;
        }
        
        .action-buttons {
            width: 100%;
            justify-content: space-between;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }

        .tickets-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .filters {
            flex-direction: column;
            width: 100%;
        }
        
        .dashboard-tabs {
            flex-direction: column;
        }
        
        .dashboard-tab-btn {
            text-align: left;
            border-bottom: 1px solid #e8f5e8;
            border-left: 3px solid transparent;
        }
        
        .dashboard-tab-btn.active {
            border-left: 3px solid var(--secondary);
            border-bottom: 1px solid #e8f5e8;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Responsive Design */
    @media (max-width: 1600px) {
        :root {
            --sidebar-width: clamp(220px, 22vw, 270px);
        }
    }

    /* Account Settings Styles */
    .settings-button:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .about-content:hover {
        background: #e9ecef !important;
        border: 1px solid #ddd;
    }

    .logout-button:hover {
        background: #c82333 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 1400px) {
        :root {
            --sidebar-width: clamp(230px, 24vw, 280px);
        }
        .main-content {
            padding: clamp(12px, 2vw, 18px);
        }
    }

    @media (max-width: 1200px) {
        :root {
            --sidebar-width: 250px;
        }
        .dashboard-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        :root {
            --sidebar-width: 220px;
        }
        .main-content {
            padding: 15px;
        }
        .dashboard-cards,
        .quick-actions {
            grid-template-columns: 1fr;
        }
        .account-layout {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
        }
        .main-content {
            margin-left: 200px;
            padding: 12px;
        }
        .header h1 {
            font-size: 1.4rem;
        }
    }

    @media (max-width: 576px) {
        .sidebar {
            position: fixed;
            left: -220px;
            width: 220px;
            transition: left 0.3s ease;
        }
        .sidebar.mobile-open {
            left: 0;
        }
        .main-content {
            margin-left: 0;
            padding: 10px;
            width: 100%;
        }
    }
</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
        <h2 style="display: flex; align-items: center; justify-content: center; gap: 8px;">AIHRA
        <img src="{{ asset('assets/AIHRA_Logo.png') }}" alt="AlHRA Logo" style="height: 50px; width: auto;">
  
        </h2>


    </div>
        
        <ul class="sidebar-menu">
            <li><a href="#" class="{{ $active_tab == 'dashboard' ? 'active' : '' }}" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#" class="{{ $active_tab == 'performance' ? 'active' : '' }}" data-section="performance"><i class="fas fa-chart-line"></i> Chatbot Performance</a></li>
            <li><a href="#" class="{{ $active_tab == 'feedback' ? 'active' : '' }}" data-section="feedback"><i class="fas fa-comment-alt"></i> Feedback</a></li>
            <li><a href="#" class="{{ $active_tab == 'content' ? 'active' : '' }}" data-section="content"><i class="fas fa-cogs"></i> Content Management</a></li>
            <li><a href="#" class="{{ $active_tab == 'tickets' ? 'active' : '' }}" data-section="tickets"><i class="fas fa-ticket-alt"></i> Chatbot Ticket Details</a></li>
            <li><a href="#" class="{{ $active_tab == 'account-management' ? 'active' : '' }}" data-section="account-management"><i class="fas fa-user-cog"></i> Account Management</a></li>
        </ul>
        
        <div class="sidebar-footer">
    <!-- Account Settings Link -->
    <a href="#" class="{{ $active_tab == 'account-settings' ? 'active' : '' }}" data-section="account-settings" style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; margin: 0 15px 10px 15px; background: rgba(255,255,255,0.1); border-radius: 8px; color: white; text-decoration: none; transition: all 0.3s; border: 1px solid rgba(255,255,255,0.2);">
        <i class="fas fa-user-circle" style="font-size: 1.1rem;"></i>
        <span style="font-weight: 500;">Account Settings</span>
    </a>
    
    <div class="account-info">
        <div class="account-avatar">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('uploads/'.Auth::user()->profile_picture) }}" alt="Profile">
            @else
                <span class="account-avatar-text">{{ substr(Auth::user()->firstName, 0, 1) }}{{ substr(Auth::user()->lastName, 0, 1) }}</span>
            @endif
        </div>
        <div class="account-details">
            <div class="account-name">{{ Auth::user()->firstName }} {{ Auth::user()->lastName }}</div>
            <div class="account-role">System Administrator</div>
        </div>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>
        <button class="logout-btn" title="Log Out">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div>
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 id="page-title">Dashboard Overview</h1>
            </div>
            <div class="header-info">
    <div class="date-range-filter" id="dateRangeFilterContainer">
        <label for="dateRangeSelect" style="margin-right: 8px; font-weight: 500; color: var(--primary);">
            <i class="fas fa-calendar-alt"></i> Range:
        </label>
        <select id="dateRangeSelect" onchange="applyDateRangeFilter()" style="padding: 8px 15px; border: 1px solid #e0efe5; border-radius: 20px; background: white; color: var(--primary); font-size: 0.9rem; cursor: pointer; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);">
            <option value="daily">Today</option>
            <option value="weekly">This Week</option>
            <option value="monthly">This Month</option>
            <option value="annually">This Year</option>
            <option value="overall" selected>Overall</option>
        </select>
        <span id="dateRangeDisplay" style="margin-left: 10px; color: #666; font-size: 0.85rem;">All Time</span>
    </div>
    <div class="user-account">
        <div class="user-avatar">
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('uploads/'.Auth::user()->profile_picture) }}" alt="Profile">
            @else
                <span class="user-avatar-text">{{ substr(Auth::user()->firstName, 0, 1) }}{{ substr(Auth::user()->lastName, 0, 1) }}</span>
            @endif
        </div>
        <span>{{ Auth::user()->firstName }}</span>
    </div>
</div>
        </div>
        
        <!-- Dashboard Section (Default) -->
        <div id="dashboard" class="section-content active">
            <!-- Stats Cards - ONLY IN DASHBOARD -->
            <div class="dashboard-cards">
                <div class="card stat-card">
                    <h3>Active Users</h3>
                    <div class="value">{{ $activeUsers }}</div>
                    <div class="trend">Total Active</div>
                </div>
                <div class="card stat-card">
                    <h3>Interactions</h3>
                    <div class="value">{{ $totalInteractions }}</div>
                    <div class="trend">Total Queries</div>
                </div>
                <div class="card stat-card">
                    <h3>Escalated Queries</h3>
                    <div class="value">{{ $escalatedQueries }}</div>
                    <div class="trend">Total Tickets</div>
                </div>
                <div class="card stat-card">
                    <h3>Resolution Rate</h3>
                    <div class="value">
                        @if($totalInteractions > 0)
                            {{ number_format(($resolvedQueries / $totalInteractions) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="trend">Success Rate</div>
                </div>
            </div>
            
            <!-- Tabs for different sections -->
            <div class="dashboard-tabs">
                <button class="dashboard-tab-btn active" data-tab="overview">Overview</button>
                <button class="dashboard-tab-btn" data-tab="performance">Performance</button>
                <button class="dashboard-tab-btn" data-tab="feedback">Feedback</button>
            </div>
            
            <!-- Overview Tab -->
            <div id="overview" class="dashboard-tab-content active">
                <div class="chart-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08); margin-bottom: 20px;">
                    <h3>Resolution Breakdown</h3>
                    <div class="chart-box" style="height: 400px;">
                        <canvas id="resolutionChart"></canvas>
                    </div>
                </div>
                
                <div class="topics-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3>Most Asked Topics</h3>
                    @forelse($mostAskedTopics as $topic)
                    <div class="topic-item">
                        <span class="topic-name">{{ $topic['topic'] }}</span>
                        <span class="topic-count">{{ $topic['count'] }} inquir{{ $topic['count'] == 1 ? 'y' : 'ies' }}</span>
                    </div>
                    @empty
                    <div class="topic-item">
                        <span class="topic-name" style="color: #666; font-style: italic;">No data available yet</span>
                        <span class="topic-count">0 inquiries</span>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Performance Tab -->
            <div id="performance" class="dashboard-tab-content">
                <!-- Average Response Time Card -->
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08); margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0; color: var(--primary); font-size: 1.1rem;">
                        <i class="fas fa-clock"></i> Average Response Time
                    </h3>
                    <div id="avgResponseTimeDisplay" style="font-size: 2.5rem; font-weight: bold; color: var(--secondary); margin: 10px 0;">
                        @if($avgResponseTime)
                            {{ number_format($avgResponseTime, 2) }}s
                        @else
                            N/A
                        @endif
                    </div>
                    <div style="color: #666; font-size: 0.9rem;">
                        Based on <span id="avgResponseTimeCount">{{ $totalInteractions }}</span> interactions
                    </div>
                </div>

                <div class="data-table">
                    <h3>Recent Chatbot Interactions ({{ $recentInteractions->total() }} total)</h3>
                    <table id="interactionsTable">
                        <thead>
                            <tr>
                                <th>Query</th>
                                <th class="sortable" onclick="sortTable('interactionsTable', 1, 'number')">Response Time</th>
                                <th class="sortable" onclick="sortTable('interactionsTable', 2, 'text')">Status</th>
                                <th class="sortable" onclick="sortTable('interactionsTable', 3, 'date')">Date and Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInteractions as $interaction)
                            <tr data-date="{{ \Carbon\Carbon::parse($interaction->questionTime)->format('Y-m-d') }}" 
                                data-timestamp="{{ \Carbon\Carbon::parse($interaction->questionTime)->format('Y-m-d H:i:s') }}" 
                                data-escalated="{{ $interaction->isEscalated ? '1' : '0' }}"
                                data-response-time="{{ isset($interaction->response_time_seconds) && $interaction->response_time_seconds !== null ? number_format($interaction->response_time_seconds, 2, '.', '') : '0.00' }}">
                                <td>{{ \Illuminate\Support\Str::limit($interaction->question, 50) }}</td>
                                <td>
                                    @if(isset($interaction->response_time_seconds) && $interaction->response_time_seconds !== null)
                                        {{ number_format($interaction->response_time_seconds, 2) }}s
                                    @else
                                        0.00s
                                    @endif
                                </td>
                                <td>
                                    <span class="status {{ $interaction->isEscalated ? 'flagged' : 'normal' }}">
                                        {{ $interaction->isEscalated ? 'Escalated' : 'Normal' }}
                                    </span>
                                </td>
                                <td data-sort="{{ \Carbon\Carbon::parse($interaction->questionTime)->timestamp }}">{{ \Carbon\Carbon::parse($interaction->questionTime)->format('d/m/y H:i:s') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #666;">
                                    No interactions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($recentInteractions->hasPages())
                    <div style="margin-top: 20px;">
                        <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                            Showing {{ $recentInteractions->firstItem() }} to {{ $recentInteractions->lastItem() }} of {{ $recentInteractions->total() }} results
                        </div>
                        <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px;">
                            @if ($recentInteractions->onFirstPage())
                                <span style="padding: 8px 12px; color: #ccc;">« Previous</span>
                            @else
                                <a href="{{ $recentInteractions->appends(['active_tab' => $active_tab])->previousPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">« Previous</a>
                            @endif
                            @if ($recentInteractions->hasMorePages())
                                <a href="{{ $recentInteractions->appends(['active_tab' => $active_tab])->nextPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">Next »</a>
                            @else
                                <span style="padding: 8px 12px; color: #ccc;">Next »</span>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                            @if ($recentInteractions->currentPage() > 1)
                                <a href="{{ $recentInteractions->appends(['active_tab' => $active_tab])->url(1) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">‹</a>
                            @endif
                            @foreach(range(1, $recentInteractions->lastPage()) as $page)
                                @if($page == $recentInteractions->currentPage())
                                    <span style="padding: 6px 10px; border: 1px solid var(--secondary); border-radius: 4px; background: var(--secondary); color: white; font-weight: bold;">{{ $page }}</span>
                                @elseif($page == 1 || $page == $recentInteractions->lastPage() || abs($page - $recentInteractions->currentPage()) < 3)
                                    <a href="{{ $recentInteractions->appends(['active_tab' => $active_tab])->url($page) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">{{ $page }}</a>
                                @elseif(abs($page - $recentInteractions->currentPage()) == 3)
                                    <span style="padding: 6px 10px; color: #666;">...</span>
                                @endif
                            @endforeach
                            @if ($recentInteractions->currentPage() < $recentInteractions->lastPage())
                                <a href="{{ $recentInteractions->appends(['active_tab' => $active_tab])->url($recentInteractions->lastPage()) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">›</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                
            </div>

            <!-- Feedback Tab -->
            <div id="feedback" class="dashboard-tab-content">
                <!-- KPIs Row: Average Feedback Rating & Most Common Flagged Reason -->
                <div style="display: flex; flex-wrap: wrap; gap: 18px; margin-bottom: 18px;">
                    <!-- Average Feedback Rating KPI -->
                    <div id="avgFeedbackKPIOverview" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.07); display: flex; align-items: center; gap: 18px; min-width: 320px;">
                        <div style="font-size: 2.2rem; color: #f39c12;">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <div style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">Average Feedback Rating</div>
                            <div id="avgFeedbackValueOverview" style="font-size: 2rem; font-weight: bold; color: var(--secondary);">N/A</div>
                            <div id="avgFeedbackCountOverview" style="font-size: 0.95rem; color: #666;">Based on 0 feedbacks</div>
                        </div>
                    </div>
                    <!-- Most Common Flagged Reason KPI -->
                    <div id="commonFlaggedReasonKPIOverview" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(255, 87, 34, 0.07); display: flex; align-items: center; gap: 18px; min-width: 320px;">
                        <div style="font-size: 2.2rem; color: #ff5722;">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div>
                            <div style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">Most Common Flagged Reason</div>
                            <div id="commonFlaggedReasonValueOverview" style="font-size: 1.2rem; font-weight: bold; color: var(--secondary);">N/A</div>
                            <div id="commonFlaggedReasonCountOverview" style="font-size: 0.95rem; color: #666;">0 flags</div>
                        </div>
                    </div>
                </div>
                <!-- Feedback Analytics Charts -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                        <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">Feedback Summary</h3>
                        <canvas id="feedbackRatingChart" style="max-height: 300px;"></canvas>
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                        <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">Flagged Responses Summary</h3>
                        <canvas id="flaggedReasonChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
        <script>
                // --- Most Common Flagged Reason KPI ---
                function updateCommonFlaggedReasonKPI() {
                    // Feedback Section (should use #flaggedDashTable for Feedback tab, #flaggedSectionTable for Feedback section)
                    // Feedback tab (dashboard): #flaggedDashTable
                    const flaggedRowsTab = document.querySelectorAll('#flaggedDashTable tbody tr');
                    const reasonCountsTab = {};
                    let totalFlagsTab = 0;
                    flaggedRowsTab.forEach(row => {
                        if (row.style.display === 'none' || row.querySelector('td[colspan]')) return;
                        const reasonCell = row.cells[2];
                        if (!reasonCell) return;
                        const reason = reasonCell.textContent.trim();
                        if (!reason) return;
                        reasonCountsTab[reason] = (reasonCountsTab[reason] || 0) + 1;
                        totalFlagsTab++;
                    });
                    let mostCommonTab = 'N/A';
                    let mostCountTab = 0;
                    for (const [reason, count] of Object.entries(reasonCountsTab)) {
                        if (count > mostCountTab) {
                            mostCommonTab = reason;
                            mostCountTab = count;
                        }
                    }
                    // Feedback tab KPI
                    const valOvr = document.getElementById('commonFlaggedReasonValueOverview');
                    const countOvr = document.getElementById('commonFlaggedReasonCountOverview');
                    if (valOvr && countOvr) {
                        valOvr.textContent = mostCommonTab;
                        countOvr.textContent = `${mostCountTab} flag${mostCountTab === 1 ? '' : 's'}`;
                    }

                    // Feedback section (section view): #flaggedSectionTable
                    const flaggedRowsSection = document.querySelectorAll('#flaggedSectionTable tbody tr');
                    const reasonCountsSection = {};
                    let totalFlagsSection = 0;
                    flaggedRowsSection.forEach(row => {
                        if (row.style.display === 'none' || row.querySelector('td[colspan]')) return;
                        const reasonCell = row.cells[2];
                        if (!reasonCell) return;
                        const reason = reasonCell.textContent.trim();
                        if (!reason) return;
                        reasonCountsSection[reason] = (reasonCountsSection[reason] || 0) + 1;
                        totalFlagsSection++;
                    });
                    let mostCommonSection = 'N/A';
                    let mostCountSection = 0;
                    for (const [reason, count] of Object.entries(reasonCountsSection)) {
                        if (count > mostCountSection) {
                            mostCommonSection = reason;
                            mostCountSection = count;
                        }
                    }
                    // Feedback Section KPI
                    const valSec = document.getElementById('commonFlaggedReasonValue');
                    const countSec = document.getElementById('commonFlaggedReasonCount');
                    if (valSec && countSec) {
                        valSec.textContent = mostCommonSection;
                        countSec.textContent = `${mostCountSection} flag${mostCountSection === 1 ? '' : 's'}`;
                    }
                }
        // Store all feedback data for KPI calculation
        // Use all feedbacks if available, otherwise fallback to paginated feedbacks
        const allFeedbackData = @json((isset($allFeedbackData) && count($allFeedbackData)) ? $allFeedbackData : (isset($feedbackData) ? $feedbackData->items() : []));


        function updateAverageFeedbackKPI() {
            // Use allFeedbackData for both Feedback and Overview sections
            let ratings = [];
            if (Array.isArray(allFeedbackData)) {
                ratings = allFeedbackData.map(fb => parseFloat(fb.rating)).filter(r => !isNaN(r));
            }
            let avg = 'N/A';
            let count = ratings.length;
            if (count > 0) {
                avg = (ratings.reduce((a, b) => a + b, 0) / count).toFixed(2);
            }
            // Feedback Section
            const valSec = document.getElementById('avgFeedbackValueSection');
            const countSec = document.getElementById('avgFeedbackCountSection');
            if (valSec && countSec) {
                valSec.textContent = avg;
                countSec.textContent = `Based on ${count} feedback${count === 1 ? '' : 's'}`;
            }
            // Overview Section
            const valOvr = document.getElementById('avgFeedbackValueOverview');
            const countOvr = document.getElementById('avgFeedbackCountOverview');
            if (valOvr && countOvr) {
                valOvr.textContent = avg;
                countOvr.textContent = `Based on ${count} feedback${count === 1 ? '' : 's'}`;
            }
        }

        // Update KPIs on date range change and on load
        document.addEventListener('DOMContentLoaded', function() {
            updateAverageFeedbackKPI();
            updateCommonFlaggedReasonKPI();
        });
        window.applyDateRangeFilter = (function(orig){
            return function() {
                orig && orig.apply(this, arguments);
                updateAverageFeedbackKPI();
                updateCommonFlaggedReasonKPI();
            }
        })(window.applyDateRangeFilter);
        </script>

                <div class="data-table">
                    <h3>User Feedback</h3>
                    <table id="feedbackDashTable">
                        <thead>
                            <tr>
                                <th class="sortable" onclick="sortTable('feedbackDashTable', 0, 'number')">Feedback ID</th>
                                <th class="sortable" onclick="sortTable('feedbackDashTable', 1, 'number')">Rating</th>
                                <th>Subject</th>
                                <th class="sortable" onclick="sortTable('feedbackDashTable', 3, 'date')">Date and Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbackData as $index => $feedback)
                            <tr>
                                <td><strong>#{{ str_pad($feedback->feedbackID, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $feedback->rating)
                                            ⭐
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                    ({{ $feedback->rating }}/5)
                                </td>
                                <td>{{ $feedback->subject ?? 'No subject' }}</td>
                                <td>{{ \Carbon\Carbon::parse($feedback->timeStamp)->format('M d, Y H:i') }}</td>
                                <td>
                                    <button onclick="viewFeedback('{{ addslashes($feedback->subject ?? 'No subject') }}', '{{ addslashes($feedback->suggestion ?? 'No comment') }}', '{{ $feedback->rating }}')" 
                                        style="padding: 4px 10px; background: var(--secondary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                                    No feedback received yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($feedbackData->hasPages())
                    <div style="margin-top: 20px;">
                        <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                            Showing {{ $feedbackData->firstItem() }} to {{ $feedbackData->lastItem() }} of {{ $feedbackData->total() }} results
                        </div>
                        <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px;">
                            @if ($feedbackData->onFirstPage())
                                <span style="padding: 8px 12px; color: #ccc;">« Previous</span>
                            @else
                                <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->previousPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">« Previous</a>
                            @endif
                            @if ($feedbackData->hasMorePages())
                                <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->nextPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">Next »</a>
                            @else
                                <span style="padding: 8px 12px; color: #ccc;">Next »</span>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                            @if ($feedbackData->currentPage() > 1)
                                <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->url(1) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">‹</a>
                            @endif
                            @foreach(range(1, $feedbackData->lastPage()) as $page)
                                @if($page == $feedbackData->currentPage())
                                    <span style="padding: 6px 10px; border: 1px solid var(--secondary); border-radius: 4px; background: var(--secondary); color: white; font-weight: bold;">{{ $page }}</span>
                                @elseif($page == 1 || $page == $feedbackData->lastPage() || abs($page - $feedbackData->currentPage()) < 3)
                                    <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->url($page) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">{{ $page }}</a>
                                @elseif(abs($page - $feedbackData->currentPage()) == 3)
                                    <span style="padding: 6px 10px; color: #666;">...</span>
                                @endif
                            @endforeach
                            @if ($feedbackData->currentPage() < $feedbackData->lastPage())
                                <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->url($feedbackData->lastPage()) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">›</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <div class="data-table" style="margin-top: 30px;">
                    <h3>Flagged Responses</h3>
                    <table id="flaggedDashTable">
                        <thead>
                            <tr>
                                <th class="sortable" onclick="sortTable('flaggedDashTable', 0, 'number')">Flag ID</th>
                                <th>Query</th>
                                <th>Reason</th>
                                <th class="sortable" onclick="sortTable('flaggedDashTable', 3, 'date')">Date</th>
                                <th class="sortable" onclick="sortTable('flaggedDashTable', 4, 'text')">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flaggedResponses as $flagged)
                            <tr id="flag-row-{{ $flagged->flaggedID }}">
                                <td><strong>#{{ str_pad($flagged->flaggedID, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td title="{{ $flagged->question }}">{{ \Illuminate\Support\Str::limit($flagged->question, 50) }}</td>
                                <td>{{ $flagged->description ?? $flagged->reasonID ?? 'Unknown' }}</td>
                                <td>{{ \Carbon\Carbon::parse($flagged->timeStamp)->format('d/m/y') }}</td>
                                <td><span class="status {{ strtolower($flagged->status) }}">{{ $flagged->status }}</span></td>
                                <td>
                                    @if($flagged->status === 'Pending')
                                    <button class="btn btn-primary" style="padding: 6px 12px; margin-right: 5px;" onclick="updateFlagStatus('{{ $flagged->flaggedID }}', 'Reviewed')">
                                        Review
                                    </button>
                                    <button class="btn" style="padding: 6px 12px; background: var(--success); color: white;" onclick="updateFlagStatus('{{ $flagged->flaggedID }}', 'Resolved')">
                                        Resolved
                                    </button>
                                    @elseif($flagged->status === 'Reviewed')
                                    <button class="btn" style="padding: 6px 12px; background: var(--success); color: white;" onclick="updateFlagStatus('{{ $flagged->flaggedID }}', 'Resolved')">
                                        Resolved
                                    </button>
                                    @else
                                    <span style="color: #666;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                                    No flagged responses found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Chatbot Performance Section -->
        <div id="performance-section" class="section-content">
            <div class="data-table">
                <h3>Performance Metrics</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Trend</th>
                            <th>Target</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Response Accuracy</td>
                            <td>92%</td>
                            <td><span class="trend up">+2%</span></td>
                            <td>95%</td>
                        </tr>
                        <tr>
                            <td>User Satisfaction</td>
                            <td>88%</td>
                            <td><span class="trend up">+5%</span></td>
                            <td>90%</td>
                        </tr>
                        <tr>
                            <td>Average Response Time</td>
                            <td>1.2s</td>
                            <td><span class="trend down">-0.3s</span></td>
                            <td>1.0s</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Feedback Section -->
        <div id="feedback-section" class="section-content">
            <!-- KPIs Row: Average Feedback Rating & Most Common Flagged Reason -->
            <div style="display: flex; flex-wrap: wrap; gap: 18px; margin-bottom: 18px;">
                <!-- Average Feedback Rating KPI -->
                <div id="avgFeedbackKPISection" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.07); display: flex; align-items: center; gap: 18px; min-width: 320px;">
                    <div style="font-size: 2.2rem; color: #f39c12;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">Average Feedback Rating</div>
                        <div id="avgFeedbackValueSection" style="font-size: 2rem; font-weight: bold; color: var(--secondary);">N/A</div>
                        <div id="avgFeedbackCountSection" style="font-size: 0.95rem; color: #666;">Based on 0 feedbacks</div>
                    </div>
                </div>
                <!-- Most Common Flagged Reason KPI -->
                <div id="commonFlaggedReasonKPI" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(255, 87, 34, 0.07); display: flex; align-items: center; gap: 18px; min-width: 320px;">
                    <div style="font-size: 2.2rem; color: #ff5722;">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">Most Common Flagged Reason</div>
                        <div id="commonFlaggedReasonValue" style="font-size: 1.2rem; font-weight: bold; color: var(--secondary);">N/A</div>
                        <div id="commonFlaggedReasonCount" style="font-size: 0.95rem; color: #666;">0 flags</div>
                    </div>
                </div>
            </div>
            <div class="data-table">
                <h3>User Feedback</h3>
                <table id="feedbackSectionTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('feedbackSectionTable', 0, 'number')">Feedback ID</th>
                            <th class="sortable" onclick="sortTable('feedbackSectionTable', 1, 'number')">Rating</th>
                            <th>Subject</th>
                            <th class="sortable" onclick="sortTable('feedbackSectionTable', 3, 'date')">Date and Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbackData as $feedback)
                        <tr>
                            <td><strong>#{{ str_pad($feedback->feedbackID, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $feedback->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                                ({{ $feedback->rating }}/5)
                            </td>
                            <td>{{ $feedback->subject ?? 'No subject' }}</td>
                            <td>{{ \Carbon\Carbon::parse($feedback->timeStamp)->format('M d, Y H:i') }}</td>
                            <td>
                                <button onclick="viewFeedback('{{ addslashes($feedback->subject ?? 'No subject') }}', '{{ addslashes($feedback->suggestion ?? 'No comment') }}', '{{ $feedback->rating }}')" 
                                    style="padding: 6px 12px; background: var(--secondary); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #666;">
                                No feedback received yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($feedbackData->hasPages())
                <div style="margin-top: 20px;">
                    <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                        Showing {{ $feedbackData->firstItem() }} to {{ $feedbackData->lastItem() }} of {{ $feedbackData->total() }} results
                    </div>
                    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px;">
                        @if ($feedbackData->onFirstPage())
                            <span style="padding: 8px 12px; color: #ccc;">« Previous</span>
                        @else
                            <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->previousPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">« Previous</a>
                        @endif
                        @if ($feedbackData->hasMorePages())
                            <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->nextPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">Next »</a>
                        @else
                            <span style="padding: 8px 12px; color: #ccc;">Next »</span>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                        @if ($feedbackData->currentPage() > 1)
                            <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->url(1) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">‹</a>
                        @endif
                        @foreach(range(1, $feedbackData->lastPage()) as $page)
                            @if($page == $feedbackData->currentPage())
                                <span style="padding: 6px 10px; border: 1px solid var(--secondary); border-radius: 4px; background: var(--secondary); color: white; font-weight: bold;">{{ $page }}</span>
                            @elseif($page == 1 || $page == $feedbackData->lastPage() || abs($page - $feedbackData->currentPage()) < 3)
                                <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->url($page) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">{{ $page }}</a>
                            @elseif(abs($page - $feedbackData->currentPage()) == 3)
                                <span style="padding: 6px 10px; color: #666;">...</span>
                            @endif
                        @endforeach
                        @if ($feedbackData->currentPage() < $feedbackData->lastPage())
                            <a href="{{ $feedbackData->appends(['active_tab' => $active_tab])->url($feedbackData->lastPage()) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">›</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="data-table" style="margin-top: 30px;">
                <h3>Flagged Responses</h3>
                <table id="flaggedSectionTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('flaggedSectionTable', 0, 'number')">Flag ID</th>
                            <th>Query</th>
                            <th>Reason</th>
                            <th class="sortable" onclick="sortTable('flaggedSectionTable', 3, 'date')">Date</th>
                            <th class="sortable" onclick="sortTable('flaggedSectionTable', 4, 'text')">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flaggedResponses as $flagged)
                        <tr id="flag-row-{{ $flagged->flaggedID }}">
                            <td><strong>#{{ str_pad($flagged->flaggedID, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td title="{{ $flagged->question }}">{{ \Illuminate\Support\Str::limit($flagged->question, 50) }}</td>
                            <td>{{ $flagged->description ?? $flagged->reasonID ?? 'Unknown' }}</td>
                            <td>{{ \Carbon\Carbon::parse($flagged->timeStamp)->format('d/m/y') }}</td>
                            <td><span class="status {{ strtolower($flagged->status) }}">{{ $flagged->status }}</span></td>
                            <td style="display: flex; gap: 5px; align-items: center;">
                                @if($flagged->status === 'Pending')
                                <button class="btn btn-primary" style="padding: 6px 12px;" onclick="updateFlagStatus('{{ $flagged->flaggedID }}', 'Reviewed')">
                                    Review
                                </button>
                                <button class="btn" style="padding: 6px 12px; background: var(--success); color: white;" onclick="updateFlagStatus('{{ $flagged->flaggedID }}', 'Resolved')">
                                    Resolved
                                </button>
                                @elseif($flagged->status === 'Reviewed')
                                <button class="btn" style="padding: 6px 12px; background: var(--success); color: white;" onclick="updateFlagStatus('{{ $flagged->flaggedID }}', 'Resolved')">
                                    Resolved
                                </button>
                                @else
                                <span style="color: #666;">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                                No flagged responses found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($flaggedResponses->hasPages())
                <div style="margin-top: 20px;">
                    <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                        Showing {{ $flaggedResponses->firstItem() }} to {{ $flaggedResponses->lastItem() }} of {{ $flaggedResponses->total() }} results
                    </div>
                    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px;">
                        @if ($flaggedResponses->onFirstPage())
                            <span style="padding: 8px 12px; color: #ccc;">« Previous</span>
                        @else
                            <a href="{{ $flaggedResponses->appends(['active_tab' => $active_tab])->previousPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">« Previous</a>
                        @endif
                        @if ($flaggedResponses->hasMorePages())
                            <a href="{{ $flaggedResponses->appends(['active_tab' => $active_tab])->nextPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">Next »</a>
                        @else
                            <span style="padding: 8px 12px; color: #ccc;">Next »</span>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                        @if ($flaggedResponses->currentPage() > 1)
                            <a href="{{ $flaggedResponses->appends(['active_tab' => $active_tab])->url(1) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">‹</a>
                        @endif
                        @foreach(range(1, $flaggedResponses->lastPage()) as $page)
                            @if($page == $flaggedResponses->currentPage())
                                <span style="padding: 6px 10px; border: 1px solid var(--secondary); border-radius: 4px; background: var(--secondary); color: white; font-weight: bold;">{{ $page }}</span>
                            @elseif($page == 1 || $page == $flaggedResponses->lastPage() || abs($page - $flaggedResponses->currentPage()) < 3)
                                <a href="{{ $flaggedResponses->appends(['active_tab' => $active_tab])->url($page) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">{{ $page }}</a>
                            @elseif(abs($page - $flaggedResponses->currentPage()) == 3)
                                <span style="padding: 6px 10px; color: #666;">...</span>
                            @endif
                        @endforeach
                        @if ($flaggedResponses->currentPage() < $flaggedResponses->lastPage())
                            <a href="{{ $flaggedResponses->appends(['active_tab' => $active_tab])->url($flaggedResponses->lastPage()) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">›</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Content Management Section -->
        <div id="content" class="section-content">
            <div class="data-table">
                <h3>Content Management</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Last Updated</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Leave Policies</td>
                            <td>2025-01-20</td>
                            <td><span class="status open">Published</span></td>
                            <td>
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-secondary">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Payroll Information</td>
                            <td>2025-01-18</td>
                            <td><span class="status open">Published</span></td>
                            <td>
                                <button class="btn btn-primary">Edit</button>
                                <button class="btn btn-secondary">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Chatbot Ticket Details Section -->
        <div id="tickets" class="section-content">
            <div class="dashboard-cards">
                <div class="card stat-card">
                    <h3>Total Tickets</h3>
                    <div class="value" id="totalTickets">{{ $totalTickets ?? 0 }}</div>
                    <div class="trend" id="totalTicketsTrend" style="display: none;">
                        <span class="trend-value"></span>
                    </div>
                </div>
                <div class="card stat-card">
                    <h3>Unresolved Tickets</h3>
                    <div class="value" id="unresolvedTickets">{{ $unresolvedTickets ?? 0 }}</div>
                    <div class="trend" id="unresolvedTicketsTrend" style="display: none;">
                        <span class="trend-value"></span>
                    </div>
                </div>
                <div class="card stat-card">
                    <h3>Resolved Tickets</h3>
                    <div class="value" id="resolvedTickets">{{ $resolvedTickets ?? 0 }}</div>
                    <div class="trend" id="resolvedTicketsTrend" style="display: none;">
                        <span class="trend-value"></span>
                    </div>
                </div>
            </div>

            <!-- Ticket Charts -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 20px 0 30px 0;">
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">Tickets by Priority</h3>
                    <canvas id="ticketPriorityChart" style="max-height: 300px;"></canvas>
                </div>
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">Tickets by Status</h3>
                    <canvas id="ticketStatusChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
            
            <!-- Search and Filters -->
            <div class="tickets-header">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchTickets" placeholder="Search tickets...">
                </div>
                <div class="filters">
                    <div class="filter-group">
                        <label for="priorityFilter">Priority</label>
                        <select id="priorityFilter">
                            <option value="all">All Priority</option>
                            <option value="urgent">Urgent</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="statusFilter">Status</label>
                        <select id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="open">Open</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Tickets Table -->
            <div class="data-table">
                <h3>Chatbot Ticket Details</h3>
                <table id="ticketsTable">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Message</th>
                            <th class="sortable" onclick="sortTable('ticketsTable', 2, 'text')">Priority</th>
                            <th class="sortable" onclick="sortTable('ticketsTable', 3, 'text')">Status</th>
                            <th class="sortable" onclick="sortTable('ticketsTable', 4, 'date')">Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody">
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_no ?? $ticket->id }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($ticket->message ?? 'No message', 50) }}</td>
                            <td>
                                <span class="priority {{ $ticket->priority ?? 'medium' }}">
                                    {{ ucfirst($ticket->priority ?? 'medium') }}
                                </span>
                            </td>
                            <td>
                                <span class="status {{ $ticket->status ?? 'open' }}">
                                    {{ ucfirst($ticket->status ?? 'open') }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/y') }}</td>
                            <td class="action-buttons-cell">
                                <button class="btn-action btn-view" onclick="viewTicketModal('{{ $ticket->ticket_no ?? $ticket->id }}')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                                No tickets found in the system.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($tickets->hasPages())
                <div style="margin-top: 20px;">
                    <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                        Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} results
                    </div>
                    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px;">
                        @if ($tickets->onFirstPage())
                            <span style="padding: 8px 12px; color: #ccc;">« Previous</span>
                        @else
                            <a href="{{ $tickets->appends(['active_tab' => $active_tab])->previousPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">« Previous</a>
                        @endif
                        @if ($tickets->hasMorePages())
                            <a href="{{ $tickets->appends(['active_tab' => $active_tab])->nextPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">Next »</a>
                        @else
                            <span style="padding: 8px 12px; color: #ccc;">Next »</span>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                        @if ($tickets->currentPage() > 1)
                            <a href="{{ $tickets->appends(['active_tab' => $active_tab])->url(1) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">‹</a>
                        @endif
                        @foreach(range(1, $tickets->lastPage()) as $page)
                            @if($page == $tickets->currentPage())
                                <span style="padding: 6px 10px; border: 1px solid var(--secondary); border-radius: 4px; background: var(--secondary); color: white; font-weight: bold;">{{ $page }}</span>
                            @elseif($page == 1 || $page == $tickets->lastPage() || abs($page - $tickets->currentPage()) < 3)
                                <a href="{{ $tickets->appends(['active_tab' => $active_tab])->url($page) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">{{ $page }}</a>
                            @elseif(abs($page - $tickets->currentPage()) == 3)
                                <span style="padding: 6px 10px; color: #666;">...</span>
                            @endif
                        @endforeach
                        @if ($tickets->currentPage() < $tickets->lastPage())
                            <a href="{{ $tickets->appends(['active_tab' => $active_tab])->url($tickets->lastPage()) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">›</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Account Management Section -->
        <div id="account-management" class="section-content">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="success-message" style="background: #e8f5e8; color: #2ecc71; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #2ecc71;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-message" style="background: #ffeaea; color: #e74c3c; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #e74c3c;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="account-management-header">
                <div class="search-box">
                    <input type="text" id="searchAccounts" placeholder="🔍 Search accounts..." value="{{ $search ?? '' }}">
                </div>
                <div class="action-buttons">
                    <button class="btn-secondary" onclick="openImportModal()">
                        <i class="fas fa-file-import"></i> Import CSV
                    </button>
                    <button class="btn-secondary" onclick="exportAccounts()">
                        <i class="fas fa-file-export"></i> Export CSV
                    </button>
                    <button class="btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> Add new account
                    </button>
                </div>
            </div>

            @if(session('import_errors'))
                <div class="error-message" style="background: #fff3cd; color: #856404; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffc107; max-height: 200px; overflow-y: auto;">
                    <strong>Import Errors:</strong>
                    <ul style="margin: 5px 0 0 20px; padding: 0;">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Accounts Table -->
            <div class="table-responsive">
                <table class="enhanced-table" id="accountsTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('accountsTable', 0, 'text')">Employee ID</th>
                            <th class="sortable" onclick="sortTable('accountsTable', 1, 'text')">First Name</th>
                            <th class="sortable" onclick="sortTable('accountsTable', 2, 'text')">Last Name</th>
                            <th>Email</th>
                            <th class="sortable" onclick="sortTable('accountsTable', 4, 'role')">Role</th>
                            <th class="sortable" onclick="sortTable('accountsTable', 5, 'text')">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="accountsTableBody">
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->employeeNum }}</td>
                            <td>{{ $user->firstName }}</td>
                            <td>{{ $user->lastName }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="role-badge role-{{ strtolower($user->role) }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($user->status) }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="action-buttons-cell">
                                <button class="btn-action btn-view" onclick="viewAccount('{{ $user->employeeNum }}')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @if($user->role != 'Admin' || $user->employeeNum == Auth::user()->employeeNum)
                                <button class="btn-action btn-edit" onclick="editAccountModal('{{ $user->employeeNum }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-action btn-reset" onclick="resetPasswordModal('{{ $user->employeeNum }}')">
                                    <i class="fas fa-key"></i> Reset
                                </button>
                                @endif
                                @if($user->employeeNum != Auth::user()->employeeNum && $user->role != 'Admin')
                                <button class="btn-action btn-delete" onclick="deleteAccount('{{ $user->employeeNum }}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                No accounts found. @if($search)Try adjusting your search terms.@else Click "Add new account" to create one.@endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Laravel Default Pagination -->
            @if($users->hasPages())
            <div style="margin-top: 20px;">
                <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>
                <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px;">
                    @if ($users->onFirstPage())
                        <span style="padding: 8px 12px; color: #ccc;">« Previous</span>
                    @else
                        <a href="{{ $users->appends(['active_tab' => $active_tab, 'search' => request('search')])->previousPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">« Previous</a>
                    @endif
                    @if ($users->hasMorePages())
                        <a href="{{ $users->appends(['active_tab' => $active_tab, 'search' => request('search')])->nextPageUrl() }}" style="padding: 8px 12px; color: var(--secondary); text-decoration: none;">Next »</a>
                    @else
                        <span style="padding: 8px 12px; color: #ccc;">Next »</span>
                    @endif
                </div>
                <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                    @if ($users->currentPage() > 1)
                        <a href="{{ $users->appends(['active_tab' => $active_tab, 'search' => request('search')])->url(1) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">‹</a>
                    @endif
                    @foreach(range(1, $users->lastPage()) as $page)
                        @if($page == $users->currentPage())
                            <span style="padding: 6px 10px; border: 1px solid var(--secondary); border-radius: 4px; background: var(--secondary); color: white; font-weight: bold;">{{ $page }}</span>
                        @elseif($page == 1 || $page == $users->lastPage() || abs($page - $users->currentPage()) < 3)
                            <a href="{{ $users->appends(['active_tab' => $active_tab, 'search' => request('search')])->url($page) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">{{ $page }}</a>
                        @elseif(abs($page - $users->currentPage()) == 3)
                            <span style="padding: 6px 10px; color: #666;">...</span>
                        @endif
                    @endforeach
                    @if ($users->currentPage() < $users->lastPage())
                        <a href="{{ $users->appends(['active_tab' => $active_tab, 'search' => request('search')])->url($users->lastPage()) }}" style="padding: 6px 10px; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); text-decoration: none; background: white;">›</a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Custom Pagination (Hidden, kept for backward compatibility) -->
            @if(false && $users->hasPages())
            <div class="pagination">
                {{-- Previous Page Link --}}
                <button class="pagination-btn" onclick="changePage({{ $users->currentPage() - 1 }})" 
                        {{ $users->onFirstPage() ? 'disabled' : '' }}>
                    ← Previous
                </button>
                
                {{-- Page Info --}}
                <span class="pagination-info">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </span>
                
                {{-- Page Numbers --}}
                <div class="pagination-pages">
                    @php
                        $start = max(1, $users->currentPage() - 2);
                        $end = min($users->lastPage(), $start + 4);
                        $start = max(1, $end - 4);
                    @endphp
                    
                    {{-- First Page --}}
                    @if($start > 1)
                        <button class="page-number" onclick="changePage(1)">1</button>
                        @if($start > 2)
                            <span class="pagination-ellipsis">...</span>
                        @endif
                    @endif
                    
                    {{-- Page Numbers --}}
                    @for ($i = $start; $i <= $end; $i++)
                        <button class="page-number {{ $i == $users->currentPage() ? 'active' : '' }}" 
                                onclick="changePage({{ $i }})">
                            {{ $i }}
                        </button>
                    @endfor
                    
                    {{-- Last Page --}}
                    @if($end < $users->lastPage())
                        @if($end < $users->lastPage() - 1)
                            <span class="pagination-ellipsis">...</span>
                        @endif
                        <button class="page-number" onclick="changePage({{ $users->lastPage() }})">
                            {{ $users->lastPage() }}
                        </button>
                    @endif
                </div>
                
                {{-- Next Page Link --}}
                <button class="pagination-btn" onclick="changePage({{ $users->currentPage() + 1 }})" 
                        {{ !$users->hasMorePages() ? 'disabled' : '' }}>
                    Next →
                </button>
            </div>
            @endif
        </div>
    </div>

    <style>
    /* Ensure Account Settings section content is below the header when active */
    #account-settings.section-content.active {
        margin-left: auto;
        margin-right: auto;
        max-width: 1100px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    </style>

    <!-- Account Settings Section -->
    <div id="account-settings" class="section-content">
        <div class="header-content-separator" style="height: 32px;"></div>
        <div style="width: 100%; max-width: 1000px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- Left Panel - Profile -->
                <div class="profile-panel" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 25px; color: var(--primary); font-size: 1.3rem;">Profile</h3>
                    
                    <div class="profile-image-container" style="text-align: center; margin-bottom: 25px;">
                        <img src="{{ Auth::user()->profile_picture ? asset('uploads/'.Auth::user()->profile_picture) : asset('assets/Logo.png') }}" 
                             alt="Profile Picture" class="profile-image" 
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary);">
                    </div>

                    <div class="profile-field" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Name</label>
                        <div class="profile-value" style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                            {{ Auth::user()->firstName }} {{ Auth::user()->middleName }} {{ Auth::user()->lastName }}
                        </div>
                    </div>

                    <div class="profile-field" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Email</label>
                        <div class="profile-value" style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                    <div class="profile-field" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Employee Number</label>
                        <div class="profile-value" style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                            {{ Auth::user()->employeeNum }}
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="profile-field">
                            <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Age</label>
                            <div class="profile-value" style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                                @php
                                    if (Auth::user()->dob) {
                                        $dob = new DateTime(Auth::user()->dob);
                                        $today = new DateTime();
                                        $age = $today->diff($dob)->y;
                                        echo $age;
                                    } else {
                                        echo Auth::user()->age ?? 'N/A';
                                    }
                                @endphp
                            </div>
                        </div>
                        <div class="profile-field">
                            <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">Sex</label>
                            <div class="profile-value" style="padding: 12px; background: #f8f9fa; border-radius: 8px; color: #333;">
                                {{ Auth::user()->sex ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Account Settings -->
                <div class="settings-panel" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 25px; color: var(--primary); font-size: 1.3rem;">Settings</h3>

                    <button class="settings-button" onclick="showAdminEditProfileModal()" 
                            style="width: 100%; padding: 15px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-bottom: 15px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </button>

                    <button class="settings-button" onclick="showAdminChangePasswordModal()" 
                            style="width: 100%; padding: 15px 20px; background: var(--secondary); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-bottom: 25px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fas fa-key"></i> Change Password
                    </button>

                    <div class="about-section" style="margin-bottom: 25px;">
                        <label style="font-weight: 600; color: #555; display: block; margin-bottom: 8px;">About</label>
                        <div class="about-content" onclick="showAdminAboutModal()" 
                             style="padding: 15px; background: #f8f9fa; border-radius: 8px; color: #666; min-height: 100px; cursor: pointer; transition: all 0.3s;">
                            {{ Auth::user()->about ?: 'Click to add information about yourself...' }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button" 
                                style="width: 100%; padding: 15px 20px; background: #dc3545; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <i class="fas fa-sign-out-alt"></i> Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Ticket Modal -->
    <div id="viewTicketModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ticket Details</h3>
                <button class="close-modal" onclick="closeTicketModal()">×</button>
            </div>
            <div class="modal-body">
                <div id="ticketDetailsContent">
                    <!-- Ticket details will be loaded here -->
                </div>
                <div style="display: flex; gap: 10px; margin-top: 25px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeTicketModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Account Modal -->
    <div id="viewAccountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Account Details</h3>
                <button class="close-modal" onclick="closeViewModal()">×</button>
            </div>
            <div class="modal-body">
                <div id="viewAccountContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div style="display: flex; gap: 10px; margin-top: 25px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeViewModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Account Modal -->
    <div id="createAccountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Account</h3>
                <button class="close-modal" onclick="closeCreateModal()">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.accounts.create') }}" id="createAccountForm">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="employeeNum">Employee Number *</label>
                            <input type="text" id="employeeNum" name="employeeNum" value="{{ old('employeeNum') }}" required>
                            @error('employeeNum')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" required>
                            @error('password')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" required>
                            @error('firstName')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" required>
                            @error('lastName')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="middleName">Middle Name</label>
                            <input type="text" id="middleName" name="middleName" value="{{ old('middleName') }}">
                        </div>

                        <div class="form-group">
                            <label for="role">Role *</label>
                            <select id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Employee" {{ old('role') == 'Employee' ? 'selected' : '' }}>Employee</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="HR" {{ old('role') == 'HR' ? 'selected' : '' }}>HR Manager</option>
                            </select>
                            @error('role')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sex">Gender *</label>
                            <select id="sex" name="sex" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth *</label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob') }}" max="{{ date('Y-m-d') }}" required>
                            <small id="agePreview" style="display:block;margin-top:4px;color:#555;">Age: —</small>
                            @error('dob')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group-full">
                            <label for="about">About (Optional)</label>
                            <textarea id="about" name="about" placeholder="Brief description about the user" rows="3">{{ old('about') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Account Status *</label>
                            <select id="status" name="status" required>
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Deactivated" {{ old('status') == 'Deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                            @error('status')
                                <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary">Create Account</button>
                        <button type="button" class="btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div id="importAccountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Import Accounts from CSV</h3>
                <button class="close-modal" onclick="closeImportModal()">×</button>
            </div>
            <div class="modal-body">
                <div style="background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid var(--success);">
                    <h4 style="margin: 0 0 10px 0; color: var(--primary);">📝 CSV Format Instructions:</h4>
                    <ul style="margin: 5px 0; padding-left: 20px; line-height: 1.8;">
                        <li><strong>Required headers (exact format):</strong><br>
                            <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 0.9em;">Employee Number,Email,First Name,Last Name,Middle Name,Role,Gender,Date of Birth (YYYY-MM-DD),Status</code>
                        </li>
                        <li><strong>Date format:</strong> YYYY-MM-DD (e.g., 1990-05-15) - Age must be 18-65</li>
                        <li><strong>Role options:</strong> Employee, Admin, HR</li>
                        <li><strong>Gender options:</strong> Male, Female</li>
                        <li><strong>Status options:</strong> Active, Deactivated</li>
                        <li><strong>Default password:</strong> LastName + Birth Year (e.g., for "Dela Cruz" born 1990: <code>DelaCruz1990</code>)</li>
                        <li><strong>Sample file:</strong> Check <code>sample_users_import.csv</code> in the project root folder</li>
                    </ul>
                </div>
                
                <form method="POST" action="{{ route('admin.accounts.import') }}" enctype="multipart/form-data" id="importAccountForm">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">Select CSV File *</label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required 
                               style="padding: 10px; border: 2px dashed var(--gray); border-radius: 5px; width: 100%;">
                        <small style="display: block; margin-top: 8px; color: #666;">
                            Accepted formats: .csv, .txt (max size: 2MB)
                        </small>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-file-import"></i> Import Accounts
                        </button>
                        <button type="button" class="btn-secondary" onclick="closeImportModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const birthInput = document.getElementById('dob');
        const agePreview = document.getElementById('agePreview');
        function updateAge(){
            if(!birthInput || !birthInput.value) { agePreview.textContent = 'Age: —'; return; }
            const dob = new Date(birthInput.value + 'T00:00:00');
            if(isNaN(dob.getTime())) { agePreview.textContent = 'Age: —'; return; }
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            agePreview.textContent = 'Age: ' + age + ' years';
        }
        if(birthInput){
            birthInput.addEventListener('change', updateAge);
            birthInput.addEventListener('keyup', updateAge);
            updateAge();
        }

        // Edit modal age auto-update - set up globally
        window.updateEditAge = function(){
            const editBirthInput = document.getElementById('edit_dob');
            const editAgePreview = document.getElementById('editAgePreview');
            if(!editBirthInput || !editBirthInput.value) { 
                if(editAgePreview) editAgePreview.textContent = 'Age: —'; 
                return; 
            }
            const dob = new Date(editBirthInput.value + 'T00:00:00');
            if(isNaN(dob.getTime())) { 
                if(editAgePreview) editAgePreview.textContent = 'Age: —'; 
                return; 
            }
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            if(editAgePreview) editAgePreview.textContent = 'Age: ' + age + ' years';
        };
        
        // Set up event listeners using event delegation on document
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'edit_dob') {
                window.updateEditAge();
            }
        });
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'edit_dob') {
                window.updateEditAge();
            }
        });
        
        window.refreshEditAgePreview = window.updateEditAge;
    })();
    </script>

    <!-- Edit Account Modal -->
    <div id="editAccountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Account</h3>
                <button class="close-modal" onclick="closeEditModal()">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="editAccountForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_employeeNum" name="employeeNum">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_email">Email Address *</label>
                            <input type="email" id="edit_email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_firstName">First Name *</label>
                            <input type="text" id="edit_firstName" name="firstName" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_lastName">Last Name *</label>
                            <input type="text" id="edit_lastName" name="lastName" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_middleName">Middle Name</label>
                            <input type="text" id="edit_middleName" name="middleName">
                        </div>

                        <div class="form-group">
                            <label for="edit_role">Role *</label>
                            <select id="edit_role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="Employee">Employee</option>
                                <option value="Admin">Administrator</option>
                                <option value="HR">HR Manager</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_sex">Gender *</label>
                            <select id="edit_sex" name="sex" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_dob">Date of Birth *</label>
                            <input type="date" id="edit_dob" name="dob" max="{{ date('Y-m-d') }}" required>
                            <small id="editAgePreview" style="display:block;margin-top:4px;color:#555;">Age: —</small>
                        </div>

                        <div class="form-group-full">
                            <label for="edit_about">About (Optional)</label>
                            <textarea id="edit_about" name="about" placeholder="Brief description about the user" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_status">Account Status *</label>
                            <select id="edit_status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Deactivated">Deactivated</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary">Update Account</button>
                        <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reset Password</h3>
                <button class="close-modal" onclick="closeResetModal()">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="resetPasswordForm">
                    @csrf
                    <input type="hidden" id="reset_employeeNum" name="employeeNum">
                    
                    <div class="form-group">
                        <label for="reset_password">New Password *</label>
                        <input type="password" id="reset_password" name="password" required minlength="8">
                        <small style="color: #666; font-size: 0.8rem;">Password must be at least 8 characters long</small>
                        @error('password')
                            <span style="color: #e74c3c; font-size: 0.8rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="reset_password_confirmation">Confirm Password *</label>
                        <input type="password" id="reset_password_confirmation" name="password_confirmation" required minlength="8">
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary">Reset Password</button>
                        <button type="button" class="btn-secondary" onclick="closeResetModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin Edit Profile Modal -->
    <div id="adminEditProfileModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Edit My Profile</h3>
                <button class="close-modal" onclick="closeAdminEditProfileModal()">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" id="adminProfileForm">
                    @csrf
                    
                    <div class="form-group" style="text-align: center; margin-bottom: 20px;">
                        <div class="profile-upload-container" style="position: relative; display: inline-block; margin-bottom: 10px;">
                            <img id="adminProfilePreview" class="profile-preview" 
                                 src="{{ Auth::user()->profile_picture ? asset('uploads/'.Auth::user()->profile_picture) : asset('assets/Logo.png') }}"
                                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--primary);">
                            <label for="adminProfilePictureInput" class="upload-label" 
                                   style="position: absolute; bottom: 0; right: 0; background: var(--primary); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="adminProfilePictureInput" class="file-input" 
                                   name="profile_picture" accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group" style="margin-bottom:8px;">
                            <label style="font-size:13px;">First Name *</label>
                            <input type="text" name="firstName" value="{{ Auth::user()->firstName }}" required style="padding:8px;font-size:13px;">
                        </div>

                        <div class="form-group" style="margin-bottom:8px;">
                            <label style="font-size:13px;">Last Name *</label>
                            <input type="text" name="lastName" value="{{ Auth::user()->lastName }}" required style="padding:8px;font-size:13px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-size:13px;">Middle Name</label>
                        <input type="text" name="middleName" value="{{ Auth::user()->middleName }}" style="padding:8px;font-size:13px;">
                    </div>

                    <div class="form-group">
                        <label style="font-size:13px;">Email *</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}" required style="padding:8px;font-size:13px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group" style="margin-bottom:8px;">
                            <label style="font-size:13px;">Date of Birth</label>
                            <input type="date" name="dob" value="{{ Auth::user()->dob }}" max="{{ date('Y-m-d') }}" style="padding:8px;font-size:13px;">
                        </div>

                        <div class="form-group" style="margin-bottom:8px;">
                            <label style="font-size:13px;">Sex</label>
                            <select name="sex" style="padding:8px;font-size:13px;">
                                <option value="Male" {{ Auth::user()->sex == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ Auth::user()->sex == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary" style="margin-top:12px;padding:10px 24px;font-size:15px;">Save Changes</button>
                        <button type="button" class="btn-secondary" onclick="closeAdminEditProfileModal()" style="margin-top:12px;padding:10px 24px;font-size:15px;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin Change Password Modal -->
    <div id="adminChangePasswordModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Change Password</h3>
                <button class="close-modal" onclick="closeAdminChangePasswordModal()">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.profile.update') }}" id="adminPasswordForm">
                    @csrf
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="password" required minlength="8">
                        <small style="color: #666; font-size: 0.8rem;">Password must be at least 8 characters long</small>
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password *</label>
                        <input type="password" id="new_password_confirmation" name="password_confirmation" required minlength="8">
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary">Update Password</button>
                        <button type="button" class="btn-secondary" onclick="closeAdminChangePasswordModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Admin About Modal -->
    <div id="adminAboutModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Edit About</h3>
                <button class="close-modal" onclick="closeAdminAboutModal()">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.profile.update') }}" id="adminAboutForm">
                    @csrf
                    
                    <div class="form-group">
                        <label for="admin_about">About Me</label>
                        <textarea id="admin_about" name="about" rows="6" 
                                  placeholder="Tell us about yourself..." 
                                  style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; resize: vertical;">{{ Auth::user()->about }}</textarea>
                        <small style="color: #666; font-size: 0.8rem;">Share information about your role, interests, or anything you'd like others to know.</small>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 25px;">
                        <button type="submit" class="btn-primary">Save</button>
                        <button type="button" class="btn-secondary" onclick="closeAdminAboutModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Store active tab from server
        const activeTab = '{{ $active_tab }}';

        // Store all interactions data for filtering (date + response time)
        const allInteractionsData = @json($allInteractionsData);

        // Store all tickets data for filtering (date + priority + status)
        const allTicketsData = @json($allTicketsData);

        // Initialize dashboard functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Restore active tab state
            restoreActiveTab();
            
            // Initialize charts
            initCharts();
            
            // Initialize dashboard tabs
            initDashboardTabs();
            
            // Initialize sidebar navigation
            initSidebarNavigation();
            
            // Initialize mobile menu
            initMobileMenu();
            
            // Initialize logout functionality
            initLogout();
            
            // Intercept all pagination links to preserve hash
            interceptPaginationLinks();
            
            // Calculate initial average response time from all data
            updateAverageResponseTime();
            
            // Update ticket trends on page load
            updateTicketTrends();
            
            // Restore scroll position after sorting (with delay to ensure content is loaded)
            const savedScrollPosition = sessionStorage.getItem('scrollPosition');
            if (savedScrollPosition) {
                setTimeout(() => {
                    window.scrollTo(0, parseInt(savedScrollPosition));
                    sessionStorage.removeItem('scrollPosition');
                }, 100);
            }
        });

        // Intercept pagination links to add current hash
        function interceptPaginationLinks() {
            // Use event delegation on the document
            document.addEventListener('click', function(e) {
                // Check if clicked element or its parent is a pagination link
                let target = e.target;
                
                // Traverse up to find an <a> tag
                while (target && target.tagName !== 'A') {
                    target = target.parentElement;
                }
                
                // If we found an <a> tag with an href
                if (target && target.tagName === 'A' && target.href) {
                    const url = new URL(target.href, window.location.origin);
                    
                    // Check if it's a pagination link (has page parameter or active_tab parameter)
                    if (url.searchParams.has('page') || url.searchParams.has('active_tab') || 
                        url.search.includes('_page=')) {
                        
                        // Get current hash
                        const currentHash = window.location.hash;
                        
                        // Only add hash if there isn't one already and we have a current hash
                        if (currentHash && !target.href.includes('#')) {
                            e.preventDefault();
                            window.location.href = target.href + currentHash;
                        }
                    }
                }
            });
        }

        // Handle hash changes (browser back/forward)
        window.addEventListener('hashchange', function() {
            const hash = window.location.hash.substring(1);
            const validSections = ['dashboard', 'performance', 'feedback', 'content', 'tickets', 'account-management'];
            
            let targetSection = null;
            
            // Handle dashboard sub-tabs
            if (hash && hash.startsWith('dashboard-')) {
                targetSection = 'dashboard';
                const targetLink = document.querySelector(`[data-section="${targetSection}"]`);
                if (targetLink) {
                    updateActiveStates(targetLink, targetSection);
                    updatePageTitle(targetSection);
                    // Restore the dashboard sub-tab
                    restoreDashboardSubTab();
                }
            } else if (hash && validSections.includes(hash)) {
                targetSection = hash;
                const targetLink = document.querySelector(`[data-section="${targetSection}"]`);
                if (targetLink) {
                    updateActiveStates(targetLink, targetSection);
                    updatePageTitle(targetSection);
                }
            }
        });

        // Restore the active tab on page load
        function restoreActiveTab() {
            // Check URL hash first
            const hash = window.location.hash.substring(1);
            const validSections = ['dashboard', 'performance', 'feedback', 'content', 'tickets', 'account-management'];
            
            console.log('Restoring active tab - Hash:', hash, 'Active Tab:', activeTab);
            
            let targetSection = null;
            
            // Handle dashboard sub-tabs (e.g., dashboard-performance)
            if (hash && hash.startsWith('dashboard-')) {
                targetSection = 'dashboard';
                console.log('Using dashboard section with sub-tab:', hash);
            } else if (hash && validSections.includes(hash)) {
                targetSection = hash;
                console.log('Using hash:', targetSection);
            } else if (activeTab && activeTab !== 'dashboard') {
                targetSection = activeTab;
                console.log('Using activeTab:', targetSection);
            }
            
            if (targetSection) {
                const targetLink = document.querySelector(`[data-section="${targetSection}"]`);
                if (targetLink) {
                    console.log('Restoring section:', targetSection);
                    updateActiveStates(targetLink, targetSection);
                    updatePageTitle(targetSection);
                } else {
                    console.warn('Target link not found for section:', targetSection);
                }
            } else {
                console.log('No section to restore, staying on dashboard');
            }
        }

        // Initialize sidebar navigation
        function initSidebarNavigation() {
            const sidebarLinks = document.querySelectorAll('.sidebar-menu a, .sidebar-footer a[data-section]');
            
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the target section
                    const targetSection = this.getAttribute('data-section');
                    
                    console.log('Sidebar clicked - Target section:', targetSection);
                    console.log('Current hash before change:', window.location.hash);
                    
                    // Update URL hash to preserve tab state
                    // For dashboard, default to overview sub-tab
                    let hashToSet = targetSection;
                    if (targetSection === 'dashboard') {
                        const currentHash = window.location.hash.substring(1);
                        // If already on a dashboard sub-tab, keep it; otherwise default to overview
                        if (!currentHash.startsWith('dashboard-')) {
                            hashToSet = 'dashboard-overview';
                        }
                    }
                    
                    if (window.location.hash !== '#' + hashToSet) {
                        window.location.hash = hashToSet;
                        console.log('Hash updated to:', window.location.hash);
                    }
                    
                    // Update active states
                    updateActiveStates(this, targetSection);
                    
                    // Update page title
                    updatePageTitle(targetSection);
                    
                    // Close mobile sidebar if open
                    if (window.innerWidth <= 768) {
                        document.querySelector('.sidebar').classList.remove('active');
                    }
                });
            });
        }

        // Update active states for sidebar and content
        function updateActiveStates(clickedLink, targetSection) {
            // Remove active class from all sidebar links (including footer link)
            document.querySelectorAll('.sidebar-menu a, .sidebar-footer a[data-section]').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked link
            clickedLink.classList.add('active');
            
            // Hide all section contents
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show target section
            // Handle the duplicate ID issue by mapping to correct section IDs
            let actualSectionId = targetSection;
            if (targetSection === 'performance') {
                actualSectionId = 'performance-section';
            } else if (targetSection === 'feedback') {
                actualSectionId = 'feedback-section';
            }
            
            document.getElementById(actualSectionId).classList.add('active');
        }

        // Update page title based on active section
        function updatePageTitle(section) {
            const pageTitle = document.getElementById('page-title');
            const filterContainer = document.getElementById('dateRangeFilterContainer');
            const userAccount = document.querySelector('.user-account');
            const header = document.querySelector('.header');
            
            switch(section) {
                case 'dashboard':
                    pageTitle.textContent = 'Dashboard Overview';
                    if (filterContainer) filterContainer.style.display = 'flex';
                    if (userAccount) userAccount.style.display = 'flex';
                    if (header) header.style.display = 'flex';
                    break;
                case 'performance':
                    pageTitle.textContent = 'Chatbot Performance';
                    if (filterContainer) filterContainer.style.display = 'none';
                    if (userAccount) userAccount.style.display = 'flex';
                    if (header) header.style.display = 'flex';
                    break;
                case 'feedback':
                    pageTitle.textContent = 'User Feedback';
                    if (filterContainer) filterContainer.style.display = 'flex';
                    if (userAccount) userAccount.style.display = 'flex';
                    if (header) header.style.display = 'flex';
                    break;
                case 'content':
                    pageTitle.textContent = 'Content Management';
                    if (filterContainer) filterContainer.style.display = 'none';
                    if (userAccount) userAccount.style.display = 'flex';
                    if (header) header.style.display = 'flex';
                    break;
                case 'tickets':
                    pageTitle.textContent = 'Chatbot Ticket Details';
                    if (filterContainer) filterContainer.style.display = 'flex';
                    if (userAccount) userAccount.style.display = 'flex';
                    if (header) header.style.display = 'flex';
                    break;
                case 'account-management':
                    pageTitle.textContent = 'Account Management';
                    if (filterContainer) filterContainer.style.display = 'none';
                    if (userAccount) userAccount.style.display = 'flex';
                    if (header) header.style.display = 'flex';
                    break;
                case 'account-settings':
                    pageTitle.textContent = 'Account Settings';
                    if (filterContainer) filterContainer.style.display = 'none';
                    if (userAccount) userAccount.style.display = 'none';
                    if (header) header.style.display = 'flex';
                    break;
            }
        }

        // Initialize dashboard tabs
        function initDashboardTabs() {
            const tabButtons = document.querySelectorAll('.dashboard-tab-btn');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Check if we're on the dashboard section
                    const dashboardSection = document.getElementById('dashboard');
                    if (dashboardSection && dashboardSection.classList.contains('active')) {
                        // Update hash to include sub-tab
                        window.location.hash = 'dashboard-' + targetTab;
                    }
                    
                    // Remove active class from all tab buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Hide all tab contents
                    document.querySelectorAll('.dashboard-tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show target tab content
                    document.getElementById(targetTab).classList.add('active');
                    
                    // Update average response time when switching to performance tab
                    if (targetTab === 'performance') {
                        updateAverageResponseTime();
                    }
                    
                    // Update feedback charts when switching to feedback tab
                    if (targetTab === 'feedback' && typeof initFeedbackCharts === 'function') {
                        initFeedbackCharts();
                    }
                    
                    // Update ticket trends when switching to chatbot tickets tab
                    if (targetTab === 'chatbot-tickets' && typeof updateTicketTrends === 'function') {
                        updateTicketTrends();
                    }
                });
            });
            
            // Restore sub-tab from hash on page load
            restoreDashboardSubTab();
        }
        
        // Restore dashboard sub-tab based on hash
        function restoreDashboardSubTab() {
            const hash = window.location.hash.substring(1);
            let subTab = 'overview'; // default
            
            console.log('[restoreDashboardSubTab] Current hash:', hash);
            
            if (hash && hash.startsWith('dashboard-')) {
                const extractedSubTab = hash.replace('dashboard-', '');
                const validSubTabs = ['overview', 'performance', 'feedback'];
                
                if (validSubTabs.includes(extractedSubTab)) {
                    subTab = extractedSubTab;
                    console.log('[restoreDashboardSubTab] Valid subtab found:', subTab);
                } else {
                    console.log('[restoreDashboardSubTab] Invalid subtab:', extractedSubTab);
                }
            } else if (hash === 'dashboard') {
                // If just #dashboard, default to overview and update the hash
                console.log('[restoreDashboardSubTab] Defaulting to overview');
                window.location.hash = 'dashboard-overview';
            } else {
                console.log('[restoreDashboardSubTab] No valid hash, defaulting to overview');
            }
            
            // Activate the correct sub-tab
            document.querySelectorAll('.dashboard-tab-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-tab') === subTab) {
                    btn.classList.add('active');
                }
            });
            
            // Show the correct sub-tab content
            document.querySelectorAll('.dashboard-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            const targetContent = document.getElementById(subTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        }

        // Initialize mobile menu
        function initMobileMenu() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            
            mobileMenuBtn.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        }

        // =============================================
        // DATE RANGE FILTER FUNCTIONALITY
        // =============================================
        
        let currentDateRange = 'overall'; // Default to overall
        let dateRangeData = {
            start: null,
            end: null,
            label: 'Overall'
        };

        // Apply date range filter
        function applyDateRangeFilter() {
            const rangeSelect = document.getElementById('dateRangeSelect');
            const rangeDisplay = document.getElementById('dateRangeDisplay');
            currentDateRange = rangeSelect.value;
            
            // Calculate date range based on selection
            const today = new Date();
            let startDate, endDate, displayText;
            
            switch(currentDateRange) {
                case 'daily':
                    startDate = new Date(today.setHours(0, 0, 0, 0));
                    endDate = new Date(today.setHours(23, 59, 59, 999));
                    displayText = formatDate(startDate);
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'Today'
                    };
                    break;
                    
                case 'weekly':
                    const firstDayOfWeek = today.getDate() - today.getDay();
                    startDate = new Date(today.setDate(firstDayOfWeek));
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
                    displayText = formatDate(startDate) + ' - ' + formatDate(endDate);
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'This Week'
                    };
                    break;
                    
                case 'monthly':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    endDate.setHours(23, 59, 59, 999);
                    displayText = startDate.toLocaleString('default', { month: 'long', year: 'numeric' });
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'This Month'
                    };
                    break;
                    
                case 'annually':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    endDate.setHours(23, 59, 59, 999);
                    displayText = today.getFullYear().toString();
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'This Year'
                    };
                    break;
                    
                case 'overall':
                    startDate = null;
                    endDate = null;
                    displayText = 'All Time';
                    dateRangeData = {
                        start: null,
                        end: null,
                        label: 'Overall'
                    };
                    break;
            }
            
            // Update display
            rangeDisplay.textContent = displayText;
            
            // Fetch filtered KPIs from server
            fetchFilteredKPIs(currentDateRange);
            
            // Apply filter to visible data
            filterDataByDateRange();
            
            console.log('Date range applied:', currentDateRange, dateRangeData);
        }

        // Format date for display
        function formatDate(date) {
            const options = { month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Filter visible data by date range
        function filterDataByDateRange() {
            const { start, end } = dateRangeData;
            
            // Filter tickets table
            filterTicketsByDate(start, end);
            
            // Filter feedback table
            filterFeedbackByDate(start, end);
            
            // Filter interactions table
            filterInteractionsByDate(start, end);
            
            // Filter flagged responses
            filterFlaggedByDate(start, end);
            
            // Update feedback and flagged charts
            if (typeof initFeedbackCharts === 'function') {
                initFeedbackCharts();
            }
            
            // Update ticket charts
            if (typeof initTicketCharts === 'function') {
                initTicketCharts();
            }
            
            // Update average response time
            updateAverageResponseTime();
            
            // Update statistics
            updateStatistics();
            // Update flagged reason KPI
            updateCommonFlaggedReasonKPI();
        }

        // Filter tickets by date range
        function filterTicketsByDate(startDate, endDate) {
            const ticketRows = document.querySelectorAll('#ticketsTable tbody tr');
            
            ticketRows.forEach(row => {
                const dateCell = row.querySelector('td:nth-child(5)'); // Date column
                if (!dateCell || !dateCell.textContent.trim()) {
                    return;
                }
                
                const rowDate = parseDateFromCell(dateCell.textContent);
                
                if (startDate === null && endDate === null) {
                    // Overall - show all
                    row.style.display = '';
                } else if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Filter feedback by date range
        function filterFeedbackByDate(startDate, endDate) {
            const feedbackRows = document.querySelectorAll('#feedbackDashTable tbody tr:not([colspan]), #feedbackSectionTable tbody tr:not([colspan])');
            
            feedbackRows.forEach(row => {
                const dateCell = row.cells[3]; // Date is in 4th column (index 3)
                if (!dateCell || !dateCell.textContent.trim()) {
                    return;
                }
                
                const rowDate = parseDateFromCell(dateCell.textContent);
                
                if (startDate === null && endDate === null) {
                    row.style.display = '';
                } else if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Filter interactions by date range
        function filterInteractionsByDate(startDate, endDate) {
            const interactionRows = document.querySelectorAll('#interactionsTable tbody tr');
            
            interactionRows.forEach(row => {
                // Skip empty state rows
                if (row.querySelector('td[colspan]')) {
                    return;
                }
                
                const dateStr = row.dataset.date;
                if (!dateStr) {
                    return;
                }
                
                const rowDate = new Date(dateStr + 'T00:00:00');
                
                if (startDate === null && endDate === null) {
                    row.style.display = '';
                } else if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Filter flagged responses by date range
        function filterFlaggedByDate(startDate, endDate) {
            // Select flagged responses from both dashboard feedback tab and feedback section
            const flaggedTables = document.querySelectorAll('#feedback .data-table:last-child tbody tr, #feedback-section .data-table:last-child tbody tr');
            
            flaggedTables.forEach(row => {
                const dateCell = row.querySelector('td:nth-child(4)'); // Date column is 4th for flagged (after removing User column)
                if (!dateCell || !dateCell.textContent.trim() || row.querySelector('td[colspan]')) {
                    return;
                }
                
                const rowDate = parseDateFromCell(dateCell.textContent);
                
                if (startDate === null && endDate === null) {
                    row.style.display = '';
                } else if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Parse date from table cell (handles multiple formats)
        function parseDateFromCell(dateText) {
            dateText = dateText.trim();
            
            // Handle format: dd/mm/yy
            if (dateText.match(/^\d{1,2}\/\d{1,2}\/\d{2}$/)) {
                const parts = dateText.split('/');
                const day = parseInt(parts[0]);
                const month = parseInt(parts[1]) - 1;
                const year = 2000 + parseInt(parts[2]);
                return new Date(year, month, day);
            }
            
            // Handle format: Month dd, yyyy hh:mm
            if (dateText.match(/^[A-Za-z]+\s+\d{1,2},\s+\d{4}/)) {
                return new Date(dateText);
            }
            
            // Default: try to parse as-is
            const parsed = new Date(dateText);
            return isNaN(parsed.getTime()) ? new Date() : parsed;
        }

        // Update statistics based on filtered data
        function updateStatistics() {
            const { start, end } = dateRangeData;
            
            // Filter tickets from allTicketsData based on date range
            let filteredTickets = allTicketsData;
            
            if (start !== null && end !== null) {
                filteredTickets = allTicketsData.filter(ticket => {
                    const ticketDate = new Date(ticket.ticket_date + 'T00:00:00');
                    return ticketDate >= start && ticketDate <= end;
                });
            }
            
            // Count tickets accurately from filtered data
            const totalVisible = filteredTickets.length;
            const unresolvedVisible = filteredTickets.filter(ticket => 
                ['Open', 'Replied', 'Waiting for HR'].includes(ticket.status)
            ).length;
            const resolvedVisible = filteredTickets.filter(ticket => 
                ticket.status === 'Resolved'
            ).length;
            
            console.log('updateStatistics:', {
                totalVisible,
                unresolvedVisible,
                resolvedVisible,
                dateRange: currentDateRange
            });
            
            // Update ticket stats if on tickets page
            const totalTicketsEl = document.getElementById('totalTickets');
            const unresolvedTicketsEl = document.getElementById('unresolvedTickets');
            const resolvedTicketsEl = document.getElementById('resolvedTickets');
            
            if (totalTicketsEl) totalTicketsEl.textContent = totalVisible;
            if (unresolvedTicketsEl) unresolvedTicketsEl.textContent = unresolvedVisible;
            if (resolvedTicketsEl) resolvedTicketsEl.textContent = resolvedVisible;
            
            // Update ticket trends
            updateTicketTrends();
            
            // Update dashboard KPIs and chart
            updateDashboardKPIs();
        }
        
        // Update ticket trends based on date range filter
        function updateTicketTrends() {
            const { start, end } = dateRangeData;
            
            console.log('updateTicketTrends called', { start, end, currentDateRange });
            
            // Hide trends for overall view
            if (start === null || end === null || currentDateRange === 'overall') {
                document.getElementById('totalTicketsTrend').style.display = 'none';
                document.getElementById('unresolvedTicketsTrend').style.display = 'none';
                document.getElementById('resolvedTicketsTrend').style.display = 'none';
                console.log('Trends hidden (overall view)');
                return;
            }
            
            // Calculate comparison period based on current date range
            let comparisonStart, comparisonEnd, comparisonLabel;
            const currentPeriodDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            
            switch(currentDateRange) {
                case 'daily':
                    // Compare with yesterday
                    comparisonEnd = new Date(start);
                    comparisonEnd.setDate(comparisonEnd.getDate() - 1);
                    comparisonEnd.setHours(23, 59, 59, 999);
                    comparisonStart = new Date(comparisonEnd);
                    comparisonStart.setHours(0, 0, 0, 0);
                    comparisonLabel = 'yesterday';
                    break;
                case 'weekly':
                    // Compare with last week
                    comparisonEnd = new Date(start);
                    comparisonEnd.setDate(comparisonEnd.getDate() - 1);
                    comparisonEnd.setHours(23, 59, 59, 999);
                    comparisonStart = new Date(comparisonEnd);
                    comparisonStart.setDate(comparisonStart.getDate() - 6);
                    comparisonStart.setHours(0, 0, 0, 0);
                    comparisonLabel = 'last week';
                    break;
                case 'monthly':
                    // Compare with last month
                    comparisonEnd = new Date(start);
                    comparisonEnd.setDate(comparisonEnd.getDate() - 1);
                    comparisonEnd.setHours(23, 59, 59, 999);
                    comparisonStart = new Date(comparisonEnd);
                    comparisonStart.setDate(comparisonStart.getDate() - (currentPeriodDays - 1));
                    comparisonStart.setHours(0, 0, 0, 0);
                    comparisonLabel = 'last month';
                    break;
                case 'annually':
                    // Compare with last year
                    comparisonEnd = new Date(start);
                    comparisonEnd.setFullYear(comparisonEnd.getFullYear() - 1);
                    comparisonEnd.setHours(23, 59, 59, 999);
                    comparisonStart = new Date(end);
                    comparisonStart.setFullYear(comparisonStart.getFullYear() - 1);
                    comparisonStart.setHours(0, 0, 0, 0);
                    comparisonLabel = 'last year';
                    break;
                default:
                    return;
            }
            
            // Filter tickets for current period
            const currentTickets = allTicketsData.filter(ticket => {
                const ticketDate = new Date(ticket.ticket_date + 'T00:00:00');
                return ticketDate >= start && ticketDate <= end;
            });
            
            // Filter tickets for comparison period
            const comparisonTickets = allTicketsData.filter(ticket => {
                const ticketDate = new Date(ticket.ticket_date + 'T00:00:00');
                return ticketDate >= comparisonStart && ticketDate <= comparisonEnd;
            });
            
            // Calculate counts for current period
            const currentTotal = currentTickets.length;
            const currentUnresolved = currentTickets.filter(t => 
                ['Open', 'Replied', 'Waiting for HR'].includes(t.status)
            ).length;
            const currentResolved = currentTickets.filter(t => t.status === 'Resolved').length;
            
            // Calculate counts for comparison period
            const comparisonTotal = comparisonTickets.length;
            const comparisonUnresolved = comparisonTickets.filter(t => 
                ['Open', 'Replied', 'Waiting for HR'].includes(t.status)
            ).length;
            const comparisonResolved = comparisonTickets.filter(t => t.status === 'Resolved').length;
            
            // Calculate changes
            const totalChange = currentTotal - comparisonTotal;
            const unresolvedChange = currentUnresolved - comparisonUnresolved;
            const resolvedChange = currentResolved - comparisonResolved;
            
            console.log('Trend calculations:', {
                currentTotal, comparisonTotal, totalChange,
                currentUnresolved, comparisonUnresolved, unresolvedChange,
                currentResolved, comparisonResolved, resolvedChange,
                comparisonLabel
            });
            
            // Update trends
            updateTrendElement('totalTicketsTrend', totalChange, comparisonLabel);
            updateTrendElement('unresolvedTicketsTrend', unresolvedChange, comparisonLabel);
            updateTrendElement('resolvedTicketsTrend', resolvedChange, comparisonLabel);
        }
        
        // Helper function to update trend element
        function updateTrendElement(elementId, change, comparisonLabel) {
            const trendEl = document.getElementById(elementId);
            if (!trendEl) {
                console.warn(`Trend element not found: ${elementId}`);
                return;
            }
            
            const trendValueEl = trendEl.querySelector('.trend-value');
            
            // Remove existing classes
            trendEl.classList.remove('up', 'down');
            
            // Add appropriate class
            if (change > 0) {
                trendEl.classList.add('up');
            } else if (change < 0) {
                trendEl.classList.add('down');
            }
            
            // Update text
            const sign = change >= 0 ? '+' : '';
            trendValueEl.textContent = `${sign}${change} from ${comparisonLabel}`;
            
            // Show trend
            trendEl.style.display = '';
            
            console.log(`Updated trend ${elementId}:`, {
                change,
                comparisonLabel,
                text: trendValueEl.textContent,
                visible: trendEl.style.display !== 'none'
            });
        }
        
        // Update dashboard KPIs based on visible filtered data
        function updateDashboardKPIs() {
            const { start, end } = dateRangeData;
            
            // Count visible filtered interactions
            const visibleInteractions = Array.from(document.querySelectorAll('#interactionsTable tbody tr'))
                .filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
            
            const totalInteractions = visibleInteractions.length;
            
            // Count escalated from visible interactions
            const escalatedQueries = visibleInteractions.filter(row => {
                return row.getAttribute('data-escalated') === '1';
            }).length;
            
            // Count pending tickets from visible tickets
            const visibleTickets = Array.from(document.querySelectorAll('#ticketsTable tbody tr'))
                .filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
            
            const pendingQueries = visibleTickets.filter(row => {
                const statusCell = row.querySelector('.status');
                const statusText = statusCell ? statusCell.textContent.toLowerCase() : '';
                return statusText.includes('open') || statusText.includes('waiting for hr');
            }).length;
            
            const resolvedQueries = totalInteractions - pendingQueries; // Resolved = Total - Pending
            
            // Update KPI cards in dashboard section
            const dashboardCards = document.querySelectorAll('#dashboard .dashboard-cards .stat-card');
            if (dashboardCards.length >= 4) {
                // Update Interactions (Total)
                const interactionsValue = dashboardCards[1].querySelector('.value');
                if (interactionsValue) interactionsValue.textContent = totalInteractions;
                
                // Update Escalated Queries
                const escalatedValue = dashboardCards[2].querySelector('.value');
                if (escalatedValue) escalatedValue.textContent = escalatedQueries;
                
                // Update Resolution Rate
                const resolutionValue = dashboardCards[3].querySelector('.value');
                if (resolutionValue) {
                    const rate = totalInteractions > 0 ? ((resolvedQueries / totalInteractions) * 100).toFixed(1) : 0;
                    resolutionValue.textContent = rate + '%';
                }
            }
            
            // Update chart
            updateChart(resolvedQueries, pendingQueries, escalatedQueries);
        }
        
        // Calculate and update average response time based on visible interactions
        function updateAverageResponseTime() {
            const { start, end } = dateRangeData;
            
            // Filter all interactions by date range
            let filteredData = allInteractionsData;
            
            if (start !== null && end !== null) {
                filteredData = allInteractionsData.filter(interaction => {
                    const interactionDate = new Date(interaction.query_date + 'T00:00:00');
                    return interactionDate >= start && interactionDate <= end;
                });
            }
            
            // Calculate average
            let totalResponseTime = 0;
            let count = 0;
            
            filteredData.forEach(interaction => {
                const responseTime = parseFloat(interaction.response_time_seconds);
                if (!isNaN(responseTime)) {
                    totalResponseTime += responseTime;
                    count++;
                }
            });
            
            const avgDisplay = document.getElementById('avgResponseTimeDisplay');
            const countDisplay = document.getElementById('avgResponseTimeCount');
            
            if (avgDisplay && countDisplay) {
                if (count > 0) {
                    const avgSeconds = totalResponseTime / count;
                    avgDisplay.textContent = avgSeconds.toFixed(2) + 's';
                    countDisplay.textContent = count;
                } else {
                    avgDisplay.textContent = 'N/A';
                    countDisplay.textContent = '0';
                }
            }
        }
        
        // Update the resolution chart with filtered data
        let resolutionChartInstance = null;
        
        function updateChart(resolved, pending, escalated) {
            const canvas = document.getElementById('resolutionChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            
            // Debug: Log chart values
            console.log('Chart Data - Resolved:', resolved, 'Pending:', pending, 'Escalated:', escalated);
            
            // Destroy existing chart if it exists
            if (resolutionChartInstance) {
                resolutionChartInstance.destroy();
            }
            
            // Create new chart with updated data
            resolutionChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Resolved', 'Pending', 'Escalated'],
                    datasets: [{
                        label: 'Queries',
                        data: [resolved, pending, escalated],
                        backgroundColor: [
                            '#2ecc71', // Green for resolved
                            '#f39c12', // Orange for pending  
                            '#3498db'  // Blue for escalated
                        ],
                        borderWidth: 0,
                        barThickness: 80,
                        maxBarThickness: 100
                    }]
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const data = dataset.data[index];
                                ctx.fillStyle = '#333';
                                ctx.font = 'bold 14px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';
                                ctx.fillText(data, bar.x, bar.y - 5);
                            });
                        });
                    }
                }],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Query Resolution Breakdown'
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.y + ' queries';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                drawBorder: false
                            },
                            title: {
                                display: true,
                                text: 'Number of Queries'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Fetch filtered KPIs from server via AJAX
        function fetchFilteredKPIs(range) {
            fetch(`/admin/kpis/filter?range=${range}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.data;
                        // Update KPI cards in dashboard section
                        const dashboardCards = document.querySelectorAll('#dashboard .dashboard-cards .stat-card');
                        if (dashboardCards.length >= 4) {
                            // Update Interactions (Total)
                            const interactionsValue = dashboardCards[1].querySelector('.value');
                            if (interactionsValue) interactionsValue.textContent = data.totalInteractions;
                            // Update Escalated Queries
                            const escalatedValue = dashboardCards[2].querySelector('.value');
                            if (escalatedValue) escalatedValue.textContent = data.escalatedQueries;
                            // Update Resolution Rate
                            const resolutionValue = dashboardCards[3].querySelector('.value');
                            if (resolutionValue) resolutionValue.textContent = data.resolutionRate + '%';
                        }
                        // Update Feedback KPIs (dashboard and feedback section)
                        if (document.getElementById('avgFeedbackValue')) {
                            document.getElementById('avgFeedbackValue').textContent = data.feedbackAvg !== null ? data.feedbackAvg : 'N/A';
                            document.getElementById('avgFeedbackCount').textContent = `Based on ${data.feedbackCount} feedback${data.feedbackCount === 1 ? '' : 's'}`;
                        }
                        if (document.getElementById('avgFeedbackValueSection')) {
                            document.getElementById('avgFeedbackValueSection').textContent = data.feedbackAvg !== null ? data.feedbackAvg : 'N/A';
                            document.getElementById('avgFeedbackCountSection').textContent = `Based on ${data.feedbackCount} feedback${data.feedbackCount === 1 ? '' : 's'}`;
                        }
                        // Update chart
                        updateChart(data.resolvedQueries, data.pendingQueries, data.escalatedQueries);
                        console.log('KPIs updated from server:', data);
                    } else {
                        console.error('Failed to fetch filtered KPIs:', result.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching filtered KPIs:', error);
                });
        }

        // Initialize date range filter on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyDateRangeFilter(); // Apply default filter (daily)
            
            // Mark sorted columns based on URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Interactions table
            const interactionsSort = urlParams.get('interactions_sort') || 'questionTime';
            const interactionsDir = urlParams.get('interactions_dir') || 'desc';
            markSortedColumn('interactionsTable', ['question', 'response_time_seconds', 'isEscalated', 'questionTime'], interactionsSort, interactionsDir);
            
            // Tickets table
            const ticketsSort = urlParams.get('tickets_sort') || 'created_at';
            const ticketsDir = urlParams.get('tickets_dir') || 'desc';
            markSortedColumn('ticketsTable', ['ticket_no', 'from_user', 'priority', 'status', 'created_at'], ticketsSort, ticketsDir);
            
            // Feedback table (both dashboard and section)
            const feedbackSort = urlParams.get('feedback_sort') || 'timeStamp';
            const feedbackDir = urlParams.get('feedback_dir') || 'desc';
            markSortedColumn('feedbackDashTable', ['feedbackID', 'rating', null, 'timeStamp'], feedbackSort, feedbackDir);
            markSortedColumn('feedbackSectionTable', ['feedbackID', 'rating', null, 'timeStamp'], feedbackSort, feedbackDir);
            
            // Flags table (all instances)
            const flagsSort = urlParams.get('flags_sort') || 'timeStamp';
            const flagsDir = urlParams.get('flags_dir') || 'desc';
            markSortedColumn('flaggedTable', ['flaggedID', null, null, 'timeStamp', 'status'], flagsSort, flagsDir);
            markSortedColumn('flaggedDashTable', ['flaggedID', null, null, 'timeStamp', 'status'], flagsSort, flagsDir);
            markSortedColumn('flaggedSectionTable', ['flaggedID', null, null, 'timeStamp', 'status'], flagsSort, flagsDir);
            // Update flagged reason KPI on load
            updateCommonFlaggedReasonKPI();
        });

        function markSortedColumn(tableId, columns, sortColumn, sortDir) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const columnIndex = columns.indexOf(sortColumn);
            if (columnIndex === -1) return;
            
            const th = table.querySelectorAll('th')[columnIndex];
            if (th) {
                th.classList.add(sortDir);
            }
        }

    // Initialize logout functionality
    function initLogout() {
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                logout();
            });
        }
    }

    // Logout function
    function logout() {
        if(confirm('Are you sure you want to log out?')) {
            // Method 1: Use the hidden form
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.submit();
            } else {
                // Method 2: Create form dynamically if hidden form doesn't exist
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    }

        // Store server-side total counts
        const serverTotalInteractions = {{ $totalInteractions }};
        const serverResolvedQueries = {{ $resolvedQueries }};
        const serverPendingQueries = {{ $pendingQueries }};
        const serverEscalatedCount = {{ $escalatedCount }};
        const serverTotalTickets = {{ $totalTickets ?? 0 }};

        // Initialize charts
        function initCharts() {
            // Initialize chart with full data, will be updated by filter
            updateChart(
                {{ $resolvedQueries }}, 
                {{ $pendingQueries }}, 
                {{ $escalatedCount }}
            );
            
            // Initialize ticket charts
            initTicketCharts();
        }

        // =============================================
        // CHATBOT TICKETS FUNCTIONALITY
        // =============================================
        let currentTicketId = null;
        let ticketPriorityChartInstance = null;
        let ticketStatusChartInstance = null;

        // Initialize ticket charts
        function initTicketCharts() {
            const { start, end } = dateRangeData;
            
            // Filter tickets by date range
            let filteredTickets = allTicketsData;
            
            if (start !== null && end !== null) {
                filteredTickets = allTicketsData.filter(ticket => {
                    const ticketDate = new Date(ticket.ticket_date + 'T00:00:00');
                    return ticketDate >= start && ticketDate <= end;
                });
            }
            
            // Count by priority
            const priorityCounts = {};
            filteredTickets.forEach(ticket => {
                const priority = ticket.priority || 'medium';
                priorityCounts[priority] = (priorityCounts[priority] || 0) + 1;
            });
            
            // Count by status
            const statusCounts = {};
            filteredTickets.forEach(ticket => {
                const status = ticket.status || 'Open';
                statusCounts[status] = (statusCounts[status] || 0) + 1;
            });
            
            // Create Priority Chart
            const priorityCanvas = document.getElementById('ticketPriorityChart');
            if (priorityCanvas) {
                const ctx = priorityCanvas.getContext('2d');
                
                if (ticketPriorityChartInstance) {
                    ticketPriorityChartInstance.destroy();
                }
                
                const priorityOrder = ['urgent', 'high', 'medium', 'low'];
                const priorityLabels = priorityOrder.map(p => p.charAt(0).toUpperCase() + p.slice(1));
                const priorityData = priorityOrder.map(p => priorityCounts[p] || 0);
                const priorityColors = {
                    'urgent': '#e74c3c',
                    'high': '#e67e22',
                    'medium': '#f39c12',
                    'low': '#3498db'
                };
                
                ticketPriorityChartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: priorityLabels,
                        datasets: [{
                            data: priorityData,
                            backgroundColor: priorityOrder.map(p => priorityColors[p]),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return context.label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Create Status Chart
            const statusCanvas = document.getElementById('ticketStatusChart');
            if (statusCanvas) {
                const ctx = statusCanvas.getContext('2d');
                
                if (ticketStatusChartInstance) {
                    ticketStatusChartInstance.destroy();
                }
                
                const statusLabels = Object.keys(statusCounts);
                const statusData = Object.values(statusCounts);
                const statusColors = {
                    'Open': '#e74c3c',
                    'Replied': '#f39c12',
                    'Waiting for HR': '#3498db',
                    'Resolved': '#2ecc71'
                };
                
                ticketStatusChartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: statusLabels.map(s => statusColors[s] || '#95a5a6'),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return context.label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Initialize tickets functionality
        document.addEventListener('DOMContentLoaded', function() {
            initTickets();
            
            // Search functionality for tickets
            const searchInput = document.getElementById('searchTickets');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#ticketsTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Filter functionality for tickets
            const priorityFilter = document.getElementById('priorityFilter');
            const statusFilter = document.getElementById('statusFilter');
            
            if (priorityFilter) {
                priorityFilter.addEventListener('change', filterTickets);
            }
            if (statusFilter) {
                statusFilter.addEventListener('change', filterTickets);
            }
        });

        function initTickets() {
            console.log('Chatbot Tickets functionality loaded');
        }

        function filterTickets() {
            const priorityFilter = document.getElementById('priorityFilter').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#ticketsTable tbody tr');
            
            rows.forEach(row => {
                const priorityElement = row.querySelector('.priority');
                const statusElement = row.querySelector('.status');
                
                if (!priorityElement || !statusElement) return;
                
                // Get actual text content and normalize it
                const priorityText = priorityElement.textContent.trim().toLowerCase();
                const statusText = statusElement.textContent.trim().toLowerCase();
                
                let showRow = true;
                
                // Check priority filter
                if (priorityFilter !== 'all' && priorityText !== priorityFilter) {
                    showRow = false;
                }
                
                // Check status filter
                if (statusFilter !== 'all' && statusText !== statusFilter) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        function viewTicketModal(ticketId) {
            currentTicketId = ticketId;
            
            console.log('Fetching ticket details for:', ticketId);
            
            // Fetch real ticket data from server
            fetch(`/admin/tickets/${encodeURIComponent(ticketId)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    if (data.success) {
                        const ticket = data.ticket;
                        
                        // Populate modal with ticket data
                        const ticketContent = `
                            <div class="ticket-details-grid">
                                <div class="ticket-detail-item">
                                    <label>Ticket Number:</label>
                                    <div class="value">${ticket.ticket_no}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Priority:</label>
                                    <div class="value">
                                        <span class="priority ${ticket.priority || 'medium'}">
                                            ${ticket.priority ? ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1) : 'Medium'}
                                        </span>
                                    </div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Status:</label>
                                    <div class="value">
                                        <span class="status ${ticket.status ? ticket.status.toLowerCase() : 'open'}">
                                            ${ticket.status || 'Open'}
                                        </span>
                                    </div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Category:</label>
                                    <div class="value">${ticket.category || 'N/A'}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Intent:</label>
                                    <div class="value">${ticket.intent || 'N/A'}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Confidence:</label>
                                    <div class="value">${ticket.confidence ? (ticket.confidence * 100).toFixed(1) + '%' : 'N/A'}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Created:</label>
                                    <div class="value">${new Date(ticket.created_at).toLocaleString()}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Last Updated:</label>
                                    <div class="value">${new Date(ticket.updated_at).toLocaleString()}</div>
                                </div>
                                <div class="ticket-detail-item ticket-message">
                                    <label>Message:</label>
                                    <div class="value">${ticket.message}</div>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('ticketDetailsContent').innerHTML = ticketContent;
                        
                        // Show modal
                        document.getElementById('viewTicketModal').classList.add('active');
                    } else {
                        console.error('Failed to load ticket:', data.message);
                        alert('Failed to load ticket details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching ticket:', error);
                    alert('Error loading ticket details: ' + error.message + '\n\nPlease check the console for more details.');
                });
        }

        function closeTicketModal() {
            document.getElementById('viewTicketModal').classList.remove('active');
            currentTicketId = null;
        }

        function resolveTicket() {
            if (currentTicketId && confirm('Are you sure you want to mark this ticket as resolved?')) {
                // In real application, you would make an API call here
                // For now, just close the modal and show success message
                alert(`Ticket ${currentTicketId} marked as resolved!`);
                closeTicketModal();
                
                // Refresh the page to show updated status, preserving the current tab
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 1000);
            }
        }

        // Close modal when clicking outside
        document.getElementById('viewTicketModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTicketModal();
            }
        });

        // =============================================
        // ACCOUNT MANAGEMENT FUNCTIONALITY
        // =============================================

        // Real-time Search Functionality for Accounts
        let searchTimeout;
        document.getElementById('searchAccounts').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value;
                const url = new URL(window.location.href);
                
                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                
                url.searchParams.set('page', '1'); // Reset to first page when searching
                // Preserve the hash when navigating
                const currentHash = window.location.hash;
                window.location.href = url.toString() + currentHash;
            }, 500); // 500ms delay
        });

        // Account Management Functions
        function openCreateModal() {
            document.getElementById('createAccountModal').classList.add('active');
        }

        function closeCreateModal() {
            document.getElementById('createAccountModal').classList.remove('active');
        }

        function exportAccounts() {
            window.location.href = '/admin/accounts/export';
        }

        function openImportModal() {
            document.getElementById('importAccountModal').classList.add('active');
        }

        function closeImportModal() {
            document.getElementById('importAccountModal').classList.remove('active');
        }

        // VIEW ACCOUNT FUNCTIONALITY
        async function viewAccount(employeeNum) {
            try {
                const response = await fetch(`/admin/accounts/${employeeNum}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                
                const user = await response.json();
                
                // Format the user data for display
                const userHtml = `
                    <div class="user-details" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div><strong>Employee Number:</strong><br>${user.employeeNum}</div>
                        <div><strong>Email:</strong><br>${user.email}</div>
                        <div><strong>First Name:</strong><br>${user.firstName}</div>
                        <div><strong>Last Name:</strong><br>${user.lastName}</div>
                        <div><strong>Middle Name:</strong><br>${user.middleName || 'N/A'}</div>
                        <div><strong>Role:</strong><br><span class="role-badge role-${user.role.toLowerCase()}">${user.role}</span></div>
                        <div><strong>Gender:</strong><br>${user.sex}</div>
                        <div><strong>Age:</strong><br>${user.age}</div>
                        <div><strong>Status:</strong><br><span class="status-badge status-${user.status.toLowerCase()}">${user.status}</span></div>
                        <div class="form-group-full">
                            <strong>About:</strong><br>
                            <div style="background: #f5f5f5; padding: 10px; border-radius: 5px; margin-top: 5px;">
                                ${user.about || 'No information provided'}
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('viewAccountContent').innerHTML = userHtml;
                document.getElementById('viewAccountModal').classList.add('active');
                
            } catch (error) {
                console.error('Error fetching user data:', error);
                alert('Error loading user data: ' + error.message);
            }
        }

        function closeViewModal() {
            document.getElementById('viewAccountModal').classList.remove('active');
        }

        // RESET PASSWORD MODAL FUNCTIONALITY
        function resetPasswordModal(employeeNum) {
            // Set the employee number
            document.getElementById('reset_employeeNum').value = employeeNum;
            
            // Set the form action to the correct route
            const resetForm = document.getElementById('resetPasswordForm');
            resetForm.action = `/admin/accounts/${employeeNum}/reset-password`;
            
            // Clear previous values
            document.getElementById('reset_password').value = '';
            document.getElementById('reset_password_confirmation').value = '';
            
            // Show the modal
            document.getElementById('resetPasswordModal').classList.add('active');
        }

        function closeResetModal() {
            document.getElementById('resetPasswordModal').classList.remove('active');
        }

        // EDIT ACCOUNT MODAL FUNCTIONALITY
        async function editAccountModal(employeeNum) {
            try {
                console.log('Fetching account data for:', employeeNum);
                
                const response = await fetch(`/admin/accounts/${employeeNum}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                
                const user = await response.json();
                console.log('User data received:', user);
                
                // Populate the edit form with user data
                document.getElementById('edit_employeeNum').value = user.employeeNum;
                document.getElementById('edit_email').value = user.email || '';
                document.getElementById('edit_firstName').value = user.firstName || '';
                document.getElementById('edit_lastName').value = user.lastName || '';
                document.getElementById('edit_middleName').value = user.middleName || '';
                document.getElementById('edit_role').value = user.role || 'Employee';
                document.getElementById('edit_sex').value = user.sex || 'Male';
                // Birth date + age preview
                const birthInput = document.getElementById('edit_dob');
                const agePrev = document.getElementById('editAgePreview');
                if (birthInput) {
                    birthInput.value = user.dob || '';
                    // Trigger age preview update
                    if (typeof window.updateEditAge === 'function') {
                        window.updateEditAge();
                    }
                }
                document.getElementById('edit_about').value = user.about || '';
                document.getElementById('edit_status').value = user.status || 'Active';
                
                // Set the form action correctly with proper method spoofing
                const editForm = document.getElementById('editAccountForm');
                editForm.action = `/admin/accounts/${employeeNum}`;
                
                // Ensure method spoofing is present
                let methodInput = editForm.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    editForm.appendChild(methodInput);
                } else {
                    methodInput.value = 'PUT';
                }
                
                console.log('Form action set to:', editForm.action);
                
                // Show modal
                document.getElementById('editAccountModal').classList.add('active');
                
            } catch (error) {
                console.error('Error fetching user data:', error);
                alert('Error loading user data: ' + error.message);
            }
        }

        function closeEditModal() {
            document.getElementById('editAccountModal').classList.remove('active');
        }

        // ADMIN ACCOUNT SETTINGS MODAL FUNCTIONS
        function showAdminEditProfileModal() {
            document.getElementById('adminEditProfileModal').classList.add('active');
        }

        function closeAdminEditProfileModal() {
            document.getElementById('adminEditProfileModal').classList.remove('active');
        }

        function showAdminChangePasswordModal() {
            document.getElementById('adminChangePasswordModal').classList.add('active');
        }

        function closeAdminChangePasswordModal() {
            document.getElementById('adminChangePasswordModal').classList.remove('active');
        }

        function showAdminAboutModal() {
            document.getElementById('adminAboutModal').classList.add('active');
        }

        function closeAdminAboutModal() {
            document.getElementById('adminAboutModal').classList.remove('active');
        }

        // Profile picture preview for admin
        document.addEventListener('DOMContentLoaded', function() {
            const adminProfileInput = document.getElementById('adminProfilePictureInput');
            if (adminProfileInput) {
                adminProfileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('adminProfilePreview').src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        // VIEW FEEDBACK FUNCTION
        function viewFeedback(subject, comment, rating) {
            const descriptions = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            const stars = '⭐'.repeat(rating) + '☆'.repeat(5 - rating);
            
            const modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 3000; display: flex; align-items: center; justify-content: center;';
            modal.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0; color: var(--primary);">Feedback Details</h3>
                        <button onclick="this.closest('div[style*=fixed]').remove()" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #666;">&times;</button>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: var(--primary);">Rating:</strong><br>
                        <span style="font-size: 24px;">${stars}</span>
                        <span style="color: #666; margin-left: 10px;">${descriptions[rating - 1]} (${rating}/5)</span>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: var(--primary);">Subject:</strong><br>
                        <p style="margin: 8px 0; padding: 10px; background: #f8f9fa; border-radius: 6px; word-wrap: break-word; overflow-wrap: break-word;">${subject}</p>
                    </div>
                    <div>
                        <strong style="color: var(--primary);">Comment:</strong><br>
                        <p style="margin: 8px 0; padding: 10px; background: #f8f9fa; border-radius: 6px; white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word;">${comment}</p>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
        }

        // DELETE ACCOUNT FUNCTION
        // Update flag status
        async function updateFlagStatus(flagId, status) {
            if (!confirm(`Mark this flagged response as ${status}?`)) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/flags/${flagId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: status })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update all instances of this flag row across all tables
                    const rows = document.querySelectorAll(`tr[id="flag-row-${flagId}"]`);
                    
                    rows.forEach(row => {
                        const statusCell = row.querySelector('td:nth-last-child(2)');
                        const actionCell = row.querySelector('td:last-child');
                        
                        // Update status badge with animation
                        if (statusCell) {
                            statusCell.innerHTML = `<span class="status ${status.toLowerCase()}">${status}</span>`;
                            statusCell.style.transition = 'background-color 0.3s ease';
                        }
                        
                        // Update action buttons based on new status
                        if (actionCell) {
                            if (status === 'Reviewed') {
                                actionCell.innerHTML = `
                                    <button class="btn" style="padding: 6px 12px; background: var(--success); color: white;" onclick="updateFlagStatus('${flagId}', 'Resolved')">
                                        Resolved
                                    </button>
                                `;
                            } else if (status === 'Resolved') {
                                actionCell.innerHTML = `<span style="color: #666;">—</span>`;
                            }
                        }
                    });
                    
                    // Show success notification
                    showNotification('Flag status updated successfully!', 'success');
                } else {
                    showNotification('Failed to update flag status.', 'error');
                }
            } catch (error) {
                console.error('Error updating flag:', error);
                showNotification('An error occurred while updating the flag.', 'error');
            }
        }

        // Show notification helper
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'var(--success)' : 'var(--danger)'};
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 3000;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function deleteAccount(employeeNum) {
            if (confirm(`Are you sure you want to delete account ${employeeNum}? The account will be archived and deactivated but data will be preserved.`)) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/accounts/${employeeNum}`;
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                // Add method spoofing for DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // =============================================
        // TABLE SORTING FUNCTIONALITY
        // =============================================
        function sortTable(tableId, columnIndex, dataType) {
            // Map table IDs to their sort parameter names and column mappings
            const sortConfig = {
                'ticketsTable': {
                    param: 'tickets',
                    columns: ['ticket_no', 'from_user', 'priority', 'status', 'created_at']
                },
                'feedbackDashTable': {
                    param: 'feedback',
                    columns: ['feedbackID', 'rating', null, 'timeStamp']
                },
                'feedbackSectionTable': {
                    param: 'feedback',
                    columns: ['feedbackID', 'rating', null, 'timeStamp']
                },
                'interactionsTable': {
                    param: 'interactions',
                    columns: ['question', 'questionTime', 'isEscalated', 'questionTime']
                },
                'flaggedTable': {
                    param: 'flags',
                    columns: ['flaggedID', null, null, 'timeStamp', 'status']
                },
                'flaggedDashTable': {
                    param: 'flags',
                    columns: ['flaggedID', null, null, 'timeStamp', 'status']
                },
                'flaggedSectionTable': {
                    param: 'flags',
                    columns: ['flaggedID', null, null, 'timeStamp', 'status']
                }
            };

            const config = sortConfig[tableId];
            if (!config) {
                // Fallback to client-side sorting for non-paginated tables
                sortTableClientSide(tableId, columnIndex, dataType);
                return;
            }

            const columnName = config.columns[columnIndex];
            // Remove hash from URL before parsing to avoid duplication
            const currentUrl = new URL(window.location.href.split('#')[0]);
            const currentSort = currentUrl.searchParams.get(config.param + '_sort');
            const currentDir = currentUrl.searchParams.get(config.param + '_dir');

            // Toggle direction
            let newDir = 'asc';
            if (currentSort === columnName && currentDir === 'asc') {
                newDir = 'desc';
            }

            // Update URL parameters
            currentUrl.searchParams.set(config.param + '_sort', columnName);
            currentUrl.searchParams.set(config.param + '_dir', newDir);
            
            // Preserve active_tab and hash
            const currentHash = window.location.hash;
            
            // Always set active_tab based on current state
            if (currentHash.startsWith('#dashboard-')) {
                // We're on a dashboard subtab
                currentUrl.searchParams.set('active_tab', 'dashboard');
            } else if (currentHash && currentHash !== '#dashboard') {
                // We're on another main tab
                const tabFromHash = currentHash.replace('#', '');
                currentUrl.searchParams.set('active_tab', tabFromHash);
            } else {
                // Use current active_tab or default to dashboard
                const currentActiveTab = currentUrl.searchParams.get('active_tab') || 'dashboard';
                currentUrl.searchParams.set('active_tab', currentActiveTab);
            }

            // Save scroll position before reload
            sessionStorage.setItem('scrollPosition', window.scrollY);
            
            // Reload page with new sort, preserving exact hash
            window.location.href = currentUrl.toString() + currentHash;
        }

        // Client-side sorting for non-paginated tables
        function sortTableClientSide(tableId, columnIndex, dataType) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
            const th = table.querySelectorAll('th')[columnIndex];
            
            // Determine sort direction
            let direction = 'asc';
            if (th.classList.contains('asc')) {
                direction = 'desc';
            }
            
            // Remove sort classes from all headers
            table.querySelectorAll('th').forEach(header => {
                header.classList.remove('asc', 'desc');
            });
            
            // Add sort class to current header
            th.classList.add(direction);
            
            // Sort rows
            rows.sort((a, b) => {
                let aValue = a.cells[columnIndex].textContent.trim();
                let bValue = b.cells[columnIndex].textContent.trim();
                
                // Handle different data types
                if (dataType === 'number') {
                    // Extract numbers from strings like "#000001" or "(3/5)"
                    aValue = parseFloat(aValue.replace(/[^0-9.-]/g, '')) || 0;
                    bValue = parseFloat(bValue.replace(/[^0-9.-]/g, '')) || 0;
                    return direction === 'asc' ? aValue - bValue : bValue - aValue;
                } else if (dataType === 'date') {
                    // Use data-sort attribute if available (timestamp), otherwise parse date text
                    const aSort = a.cells[columnIndex].getAttribute('data-sort');
                    const bSort = b.cells[columnIndex].getAttribute('data-sort');
                    
                    if (aSort && bSort) {
                        aValue = parseInt(aSort);
                        bValue = parseInt(bSort);
                    } else {
                        aValue = new Date(aValue);
                        bValue = new Date(bValue);
                    }
                    return direction === 'asc' ? aValue - bValue : bValue - aValue;
                } else if (dataType === 'role') {
                    // Custom role sorting: Admin > HR > Employee
                    const roleOrder = { 'admin': 1, 'hr': 2, 'employee': 3 };
                    const aRole = aValue.toLowerCase();
                    const bRole = bValue.toLowerCase();
                    const aOrder = roleOrder[aRole] || 999;
                    const bOrder = roleOrder[bRole] || 999;
                    return direction === 'asc' ? aOrder - bOrder : bOrder - aOrder;
                } else {
                    // Text comparison
                    aValue = aValue.toLowerCase();
                    bValue = bValue.toLowerCase();
                    if (direction === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        // =============================================
        // FEEDBACK ANALYTICS CHARTS
        // =============================================
        let feedbackRatingChartInstance = null;
        let flaggedReasonChartInstance = null;

        function initFeedbackCharts() {
            // Get feedback rating data from table (only visible rows)
            const feedbackRows = document.querySelectorAll('#feedbackDashTable tbody tr:not([colspan])');
            const ratingCounts = { '1': 0, '2': 0, '3': 0, '4': 0, '5': 0 };
            
            feedbackRows.forEach(row => {
                // Skip hidden rows (filtered out by date range)
                if (row.style.display === 'none') return;
                
                const ratingText = row.cells[1]?.textContent;
                const match = ratingText?.match(/\((\d)\/5\)/);
                if (match) {
                    const rating = match[1];
                    ratingCounts[rating]++;
                }
            });

            // Create Feedback Rating Chart
            const ratingCanvas = document.getElementById('feedbackRatingChart');
            if (ratingCanvas) {
                const ctx = ratingCanvas.getContext('2d');
                
                if (feedbackRatingChartInstance) {
                    feedbackRatingChartInstance.destroy();
                }
                
                feedbackRatingChartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['⭐ 1 Star', '⭐⭐ 2 Stars', '⭐⭐⭐ 3 Stars', '⭐⭐⭐⭐ 4 Stars', '⭐⭐⭐⭐⭐ 5 Stars'],
                        datasets: [{
                            data: [ratingCounts['1'], ratingCounts['2'], ratingCounts['3'], ratingCounts['4'], ratingCounts['5']],
                            backgroundColor: [
                                '#e74c3c', // Red for 1 star
                                '#e67e22', // Orange for 2 stars
                                '#f39c12', // Yellow for 3 stars
                                '#2ecc71', // Light green for 4 stars
                                '#27ae60'  // Dark green for 5 stars
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return context.label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Get flagged responses reason data from table (only visible rows)
            const flaggedRows = document.querySelectorAll('#flaggedDashTable tbody tr:not([colspan])');
            const reasonCounts = {};
            
            flaggedRows.forEach(row => {
                // Skip hidden rows (filtered out by date range)
                if (row.style.display === 'none') return;
                
                const reason = row.cells[2]?.textContent.trim();
                if (reason && reason !== 'Unknown') {
                    reasonCounts[reason] = (reasonCounts[reason] || 0) + 1;
                }
            });

            // Create Flagged Reason Chart
            const reasonCanvas = document.getElementById('flaggedReasonChart');
            if (reasonCanvas) {
                const ctx = reasonCanvas.getContext('2d');
                
                if (flaggedReasonChartInstance) {
                    flaggedReasonChartInstance.destroy();
                }
                
                const labels = Object.keys(reasonCounts);
                const data = Object.values(reasonCounts);
                const colors = [
                    '#e74c3c', '#3498db', '#f39c12', '#9b59b6', '#1abc9c',
                    '#e67e22', '#2ecc71', '#34495e', '#16a085', '#d35400'
                ];
                
                flaggedReasonChartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors.slice(0, labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.parsed;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return context.label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Restore active section and tab state from server-side variable
        function restoreActiveState() {
            const activeTab = '{{ $active_tab }}';
            
            // Check if there's a hash in the URL (for dashboard subtabs)
            const hash = window.location.hash;
            
            if (!activeTab || activeTab === 'dashboard') {
                // If we're on dashboard and there's a hash, navigate to that subtab
                if (hash && hash.startsWith('#dashboard-')) {
                    // The hash navigation is already handled by the browser
                    return;
                }
                return;
            }

            // Find and click the appropriate sidebar link
            const sidebarLink = document.querySelector(`.sidebar-menu a[data-section="${activeTab}"]`);
            if (sidebarLink) {
                sidebarLink.click();
            }
        }

        // Initialize charts when page loads and when feedback tab is shown
        document.addEventListener('DOMContentLoaded', function() {
            // Restore active section/tab from URL parameter
            restoreActiveState();
            
            // Initialize charts if on feedback tab
            if (document.querySelector('#feedback.dashboard-tab-content')) {
                setTimeout(initFeedbackCharts, 100);
            }
            
            // Re-initialize when feedback tab is clicked
            const feedbackTabBtn = document.querySelector('[data-tab="feedback"]');
            if (feedbackTabBtn) {
                feedbackTabBtn.addEventListener('click', function() {
                    setTimeout(initFeedbackCharts, 100);
                });
            }
        });

        // Restore active section and tab state from server-side variable
        function restoreActiveState() {
            const activeTab = '{{ $active_tab }}';
            if (!activeTab || activeTab === 'dashboard') return;

            // Find and click the appropriate sidebar link
            const sidebarLink = document.querySelector(`.sidebar-menu a[data-section="${activeTab}"]`);
            if (sidebarLink) {
                sidebarLink.click();
            }
        }
    </script>
</body>
</html>