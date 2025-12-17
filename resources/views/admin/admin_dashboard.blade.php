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
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
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
        padding: 0;
        margin: 0;
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
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
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
    .btn-archive { background: #ffebee; color: var(--danger); }

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

    /* Online indicator pulse animation */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    /* Responsive Design */
    @media (max-width: 1600px) {
        :root {
            --sidebar-width: 250px;
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
            --sidebar-width: 240px;
        }
        .main-content {
            padding: clamp(12px, 2vw, 18px);
        }
    }

    @media (max-width: 1200px) {
        :root {
            --sidebar-width: 230px;
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
        :root {
            --sidebar-width: 200px;
        }
        .sidebar {
            width: var(--sidebar-width);
        }
        .main-content {
            margin-left: var(--sidebar-width);
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
                <div class="topics-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08); margin-bottom: 20px;">
                    <h3>Most Asked Topics</h3>
                    @forelse($mostAskedTopics as $topic)
                    <div class="topic-item" style="cursor: pointer; transition: background 0.2s;" onclick="filterByTopic('{{ $topic['topic'] }}')" onmouseover="this.style.background='#f0f8f4'" onmouseout="this.style.background='transparent'">
                        <span class="topic-name">
                            @php
                                $words = explode(' ', strtolower($topic['topic']));
                                $formatted = array_map(function($word) {
                                    return strlen($word) >= 4 ? ucfirst($word) : $word;
                                }, $words);
                                echo implode(' ', $formatted);
                            @endphp
                        </span>
                        <span class="topic-count">{{ $topic['count'] }} inquir{{ $topic['count'] == 1 ? 'y' : 'ies' }}</span>
                    </div>
                    @empty
                    <div class="topic-item">
                        <span class="topic-name" style="color: #666; font-style: italic;">No data available yet</span>
                        <span class="topic-count">0 inquiries</span>
                    </div>
                    @endforelse
                </div>
                <div class="chart-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3>Resolution Breakdown</h3>
                    <div class="chart-box" style="height: 400px;">
                        <canvas id="resolutionChart"></canvas>
                    </div>
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
                    <h3>Recent Chatbot Interactions ({{ $recentInteractions->count() }} total)</h3>
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
                    <div style="margin-top: 20px;">
                        <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;" data-pagify="interactions-info"></div>
                        <div style="text-align: center;"><div style="display: inline-flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;" data-pagify="interactions-nav"></div></div>
                    </div>
                </div>
                
            </div>

            <!-- Feedback Tab -->
            <div id="feedback" class="dashboard-tab-content">
                <!-- KPI: Average Feedback Rating -->
                <div style="display: flex; gap: 24px; margin-bottom: 18px; flex-wrap: wrap;">
                    <!-- Average Feedback Rating KPI -->
                    <div id="avgFeedbackKPI" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.07); display: flex; align-items: center; gap: 18px; max-width: 400px; min-width: 260px;">
                        <div style="font-size: 2.2rem; color: #f39c12;">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <div style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">Average Feedback Rating</div>
                            <div id="avgFeedbackValue" style="font-size: 2rem; font-weight: bold; color: var(--secondary);">N/A</div>
                            <div id="avgFeedbackCount" style="font-size: 0.95rem; color: #666;">Based on 0 feedbacks</div>
                        </div>
                    </div>
                    <!-- Most Common Flagged Reason KPI -->
                    <div id="commonFlaggedReasonKPI" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.07); display: flex; align-items: center; gap: 18px; max-width: 400px; min-width: 260px;">
                        <div style="font-size: 2.2rem; color: #dc3545;">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div>
                            <div style="font-size: 1.1rem; color: var(--danger); font-weight: 600;">Most Common Flagged Reason</div>
                            <div id="commonFlaggedReasonValue" style="font-size: 1.2rem; font-weight: bold; color: var(--danger);">N/A</div>
                            <div id="commonFlaggedReasonCount" style="font-size: 0.95rem; color: #666;">Based on 0 flags</div>
                        </div>
                    </div>
                </div>

                <!-- Star Rating Filter -->
                <div style="margin-bottom: 20px; background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08); display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <label style="font-weight: 600; color: var(--primary);">Filter by Rating:</label>
                    <button onclick="filterFeedbackByStars('all', this)" class="star-filter-btn active" data-stars="all" style="padding: 8px 16px; border: 2px solid var(--primary); background: var(--primary); color: white; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">All Ratings</button>
                    <button onclick="filterFeedbackByStars('5', this)" class="star-filter-btn" data-stars="5" style="padding: 8px 16px; border: 2px solid #27ae60; background: white; color: #27ae60; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐⭐⭐⭐ 5 Stars</button>
                    <button onclick="filterFeedbackByStars('4', this)" class="star-filter-btn" data-stars="4" style="padding: 8px 16px; border: 2px solid #2ecc71; background: white; color: #2ecc71; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐⭐⭐ 4 Stars</button>
                    <button onclick="filterFeedbackByStars('3', this)" class="star-filter-btn" data-stars="3" style="padding: 8px 16px; border: 2px solid #f39c12; background: white; color: #f39c12; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐⭐ 3 Stars</button>
                    <button onclick="filterFeedbackByStars('2', this)" class="star-filter-btn" data-stars="2" style="padding: 8px 16px; border: 2px solid #e67e22; background: white; color: #e67e22; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐ 2 Stars</button>
                    <button onclick="filterFeedbackByStars('1', this)" class="star-filter-btn" data-stars="1" style="padding: 8px 16px; border: 2px solid #e74c3c; background: white; color: #e74c3c; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐ 1 Star</button>
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
                // --- Ticket KPI and Filter Logic ---
                let selectedKPI = 'all';
                function filterTicketsByKPI(kpi, el) {
                    selectedKPI = kpi;
                    // Set active class on KPI cards
                    document.querySelectorAll('#ticketKpiCards .kpi-card').forEach(card => {
                        card.classList.remove('active');
                    });
                    if (el) el.classList.add('active');
                    else document.querySelector(`#ticketKpiCards .kpi-card[data-kpi="${kpi}"]`).classList.add('active');
                    applyCombinedTicketFilters();
                }

                function applyCombinedTicketFilters() {
                    const priority = document.getElementById('priorityFilter')?.value || 'all';
                    const status = document.getElementById('statusFilter')?.value || 'all';
                    const search = document.getElementById('searchTickets')?.value?.toLowerCase() || '';
                    
                    // Get date range from central state (defined later in file, so check if exists)
                    const startDate = (typeof dateRangeData !== 'undefined') ? dateRangeData.start : null;
                    const endDate = (typeof dateRangeData !== 'undefined') ? dateRangeData.end : null;
                    
                    // Check if any filter is actually applied
                    const isFiltered = selectedKPI !== 'all' || priority !== 'all' || status !== 'all' || search !== '' || startDate !== null;
                    
                    const rows = document.querySelectorAll('#ticketsTableBody tr');
                    let total = 0, unresolved = 0, resolved = 0;
                    rows.forEach(row => {
                        // Skip empty row
                        if (row.querySelector('td[colspan]')) { row.style.display = ''; return; }
                        const prio = row.querySelector('.priority')?.textContent?.trim().toLowerCase() || '';
                        const stat = row.querySelector('.status')?.textContent?.trim().toLowerCase() || '';
                        const msg = row.cells[1]?.textContent?.toLowerCase() || '';
                        let show = true;
                        
                        // Date filter (from global date range selector)
                        if (startDate !== null && endDate !== null) {
                            const dateCell = row.querySelector('td:nth-child(5)'); // Date column
                            if (dateCell && dateCell.textContent.trim()) {
                                const rowDate = (typeof parseDateFromCell === 'function') ? 
                                    parseDateFromCell(dateCell.textContent) : new Date(dateCell.textContent);
                                if (rowDate < startDate || rowDate > endDate) show = false;
                            }
                        }
                        
                        // KPI filter
                        if (show && selectedKPI === 'unresolved' && stat !== 'open') show = false;
                        if (show && selectedKPI === 'resolved' && stat !== 'resolved') show = false;
                        // Priority filter
                        if (show && priority !== 'all' && prio !== priority) show = false;
                        // Status filter
                        if (show && status !== 'all' && stat !== status) show = false;
                        // Search filter
                        if (show && search && !msg.includes(search)) show = false;
                        row.style.display = show ? '' : 'none';
                        if (show) {
                            total++;
                            if (stat === 'open') unresolved++;
                            if (stat === 'resolved') resolved++;
                        }
                    });
                    document.getElementById('totalTickets').textContent = total;
                    document.getElementById('unresolvedTickets').textContent = unresolved;
                    document.getElementById('resolvedTickets').textContent = resolved;
                    
                    // Update pagination
                    updatePagificationDisplay('tickets', total, isFiltered);
                    
                    // Update charts to reflect filtered data
                    if (typeof initTicketCharts === 'function') {
                        initTicketCharts();
                    }
                }

                // Hook up dropdowns and search to unified filter
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('priorityFilter')?.addEventListener('change', applyCombinedTicketFilters);
                    document.getElementById('statusFilter')?.addEventListener('change', applyCombinedTicketFilters);
                    document.getElementById('searchTickets')?.addEventListener('input', applyCombinedTicketFilters);
                    // Default: show all
                    filterTicketsByKPI('all');
                });
        // Store all flagged data for KPI calculation
        let allFlaggedData = @json(isset($allFlaggedData) ? $allFlaggedData : (isset($flaggedResponses) ? $flaggedResponses : []));
        // If flaggedResponses is a Laravel Collection, convert to array
        if (allFlaggedData && typeof allFlaggedData === 'object' && allFlaggedData.data) {
            allFlaggedData = allFlaggedData.data;
        }
        // ...existing allFeedbackData code...
        const allFeedbackData = @json((isset($allFeedbackData) && count($allFeedbackData)) ? $allFeedbackData : (isset($feedbackData) ? $feedbackData->toArray() : []));

        // Update both KPIs on date range change
        function updateFlaggedReasonKPI() {
            // Use only visible rows in flaggedDashTable for KPI, matching the chart and table
            const flaggedRows = document.querySelectorAll('#flaggedDashTable tbody tr');
            const reasonCounts = {};
            let visibleCount = 0;
            flaggedRows.forEach(row => {
                if (row.style.display === 'none' || row.querySelector('td[colspan]')) return;
                const reasonCell = row.cells[2];
                let reason = reasonCell ? reasonCell.textContent.trim() : 'Unknown';
                if (!reason) reason = 'Unknown';
                reasonCounts[reason] = (reasonCounts[reason] || 0) + 1;
                visibleCount++;
            });
            let mostCommon = 'N/A', mostCount = 0;
            for (const [reason, count] of Object.entries(reasonCounts)) {
                if (count > mostCount) {
                    mostCommon = reason;
                    mostCount = count;
                }
            }
            document.getElementById('commonFlaggedReasonValue').textContent = mostCommon;
            document.getElementById('commonFlaggedReasonCount').textContent = `Based on ${visibleCount} flag${visibleCount === 1 ? '' : 's'}`;
        }

        function updateAverageFeedbackKPI() {
            // Deprecated: now handled by fetchFilteredKPIs for accuracy
        }

        // Update KPIs on date range change
        document.addEventListener('DOMContentLoaded', function() {
            updateAverageFeedbackKPI();
            updateFlaggedReasonKPI();
        });
        window.applyDateRangeFilter = (function(orig){
            return function() {
                orig && orig.apply(this, arguments);
                updateAverageFeedbackKPI();
                updateFlaggedReasonKPI();
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
                    <div style="margin-top: 20px;">
                        <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;" data-pagify="feedback-info"></div>
                        <div style="text-align: center;"><div style="display: inline-flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;" data-pagify="feedback-nav"></div></div>
                    </div>
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
                                <td>{{ $flagged->reason ?? 'Unknown' }}</td>
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
                    <div style="margin-top: 20px;">
                        <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;" data-pagify="flagged-dash-info"></div>
                        <div style="text-align: center;"><div style="display: inline-flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;" data-pagify="flagged-dash-nav"></div></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chatbot Performance Section -->
        <div id="performance-section" class="section-content">
            <!-- Date Range Filter -->
            <div style="background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08); margin-bottom: 20px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <label style="font-weight: 600; color: var(--primary);"><i class="fas fa-calendar-alt"></i> Date Range:</label>
                <input type="date" id="performanceStartDate" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem;">
                <span style="color: #666;">to</span>
                <input type="date" id="performanceEndDate" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem;">
                <button onclick="applyPerformanceDateFilter()" style="padding: 8px 16px; background: var(--secondary); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <button onclick="resetPerformanceDateFilter()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>

            <!-- KPI Cards Row -->
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 25px;">
                <!-- Total Interactions KPI -->
                <div style="background: linear-gradient(135deg, #2d5a3d 0%, #3d7a4d 100%); padding: 20px 28px; border-radius: 14px; box-shadow: 0 4px 15px rgba(45, 90, 61, 0.2); display: flex; align-items: center; gap: 18px; flex: 1; min-width: 280px;">
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 12px;">
                        <i class="fas fa-comments" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.95rem; color: rgba(255,255,255,0.85); font-weight: 500;">Total Interactions</div>
                        <div id="performanceTotalInteractions" style="font-size: 2.2rem; font-weight: bold; color: white;">{{ $totalInteractions }}</div>
                        <div id="performanceInteractionsRange" style="font-size: 0.85rem; color: rgba(255,255,255,0.7);">All time</div>
                    </div>
                </div>
                
                <!-- Average Response Time KPI -->
                <div style="background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%); padding: 20px 28px; border-radius: 14px; box-shadow: 0 4px 15px rgba(21, 101, 192, 0.2); display: flex; align-items: center; gap: 18px; flex: 1; min-width: 280px;">
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 12px;">
                        <i class="fas fa-clock" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.95rem; color: rgba(255,255,255,0.85); font-weight: 500;">Avg Response Time</div>
                        <div id="performanceAvgResponseTime" style="font-size: 2.2rem; font-weight: bold; color: white;">{{ number_format($avgResponseTime ?? 0, 2) }}s</div>
                        <div id="performanceResponseTimeRange" style="font-size: 0.85rem; color: rgba(255,255,255,0.7);">All time</div>
                    </div>
                </div>
            </div>

            <!-- Area Charts Row -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                <!-- Response Time Trend Chart -->
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">
                        <i class="fas fa-chart-area" style="margin-right: 8px;"></i>Average Response Time Trend
                    </h3>
                    <canvas id="responseTimeTrendChart" style="max-height: 300px;"></canvas>
                </div>
                
                <!-- Interactions Over Time Chart -->
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">
                        <i class="fas fa-chart-line" style="margin-right: 8px;"></i>Interactions Over Time
                    </h3>
                    <canvas id="interactionsOverTimeChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Feedback Section -->
        <div id="feedback-section" class="section-content">
            <!-- KPIs Row: Average Feedback Rating & Most Common Flagged Reason (Feedback Section) -->
            <div style="display: flex; flex-wrap: wrap; gap: 18px; margin-bottom: 18px;">
                <div id="avgFeedbackKPISection" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.07); display: flex; align-items: center; gap: 18px; max-width: 400px;">
                    <div style="font-size: 2.2rem; color: #f39c12;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.1rem; color: var(--primary); font-weight: 600;">Average Feedback Rating</div>
                        <div id="avgFeedbackValueSection" style="font-size: 2rem; font-weight: bold; color: var(--secondary);">N/A</div>
                        <div id="avgFeedbackCountSection" style="font-size: 0.95rem; color: #666;">Based on 0 feedbacks</div>
                    </div>
                </div>
                <div id="commonFlaggedReasonKPISection" style="background: white; padding: 18px 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.07); display: flex; align-items: center; gap: 18px; max-width: 400px; min-width: 260px;">
                    <div style="font-size: 2.2rem; color: #dc3545;">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.1rem; color: var(--danger); font-weight: 600;">Most Common Flagged Reason</div>
                        <div id="commonFlaggedReasonValueSection" style="font-size: 1.2rem; font-weight: bold; color: var(--danger);">N/A</div>
                        <div id="commonFlaggedReasonCountSection" style="font-size: 0.95rem; color: #666;">
                            Based on {{ $flaggedCount }} flag{{ $flaggedCount == 1 ? '' : 's' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Star Rating Filter -->
            <div style="margin-bottom: 20px; background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08); display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <label style="font-weight: 600; color: var(--primary);">Filter by Rating:</label>
                <button onclick="filterFeedbackSectionByStars('all', this)" class="star-filter-section-btn active" data-stars="all" style="padding: 8px 16px; border: 2px solid var(--primary); background: var(--primary); color: white; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">All Ratings</button>
                <button onclick="filterFeedbackSectionByStars('5', this)" class="star-filter-section-btn" data-stars="5" style="padding: 8px 16px; border: 2px solid #27ae60; background: white; color: #27ae60; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐⭐⭐⭐ 5 Stars</button>
                <button onclick="filterFeedbackSectionByStars('4', this)" class="star-filter-section-btn" data-stars="4" style="padding: 8px 16px; border: 2px solid #2ecc71; background: white; color: #2ecc71; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐⭐⭐ 4 Stars</button>
                <button onclick="filterFeedbackSectionByStars('3', this)" class="star-filter-section-btn" data-stars="3" style="padding: 8px 16px; border: 2px solid #f39c12; background: white; color: #f39c12; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐⭐ 3 Stars</button>
                <button onclick="filterFeedbackSectionByStars('2', this)" class="star-filter-section-btn" data-stars="2" style="padding: 8px 16px; border: 2px solid #e67e22; background: white; color: #e67e22; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐⭐ 2 Stars</button>
                <button onclick="filterFeedbackSectionByStars('1', this)" class="star-filter-section-btn" data-stars="1" style="padding: 8px 16px; border: 2px solid #e74c3c; background: white; color: #e74c3c; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;">⭐ 1 Star</button>
            </div>

            <!-- Feedback Analytics Charts -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">Feedback Summary</h3>
                    <canvas id="feedbackRatingChartSection" style="max-height: 300px;"></canvas>
                </div>
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);">
                    <h3 style="margin: 0 0 15px 0; color: var(--primary); font-size: 1.1rem;">Flagged Responses Summary</h3>
                    <canvas id="flaggedReasonChartSection" style="max-height: 300px;"></canvas>
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
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                                No feedback received yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 20px;">
                    <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;" data-pagify="feedback-section-info"></div>
                    <div style="text-align: center;"><div style="display: inline-flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;" data-pagify="feedback-section-nav"></div></div>
                </div>
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
                            <td>{{ $flagged->reason ?? 'Unknown' }}</td>
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
                <div style="margin-top: 20px;">
                    <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;" data-pagify="flagged-section-info"></div>
                    <div style="text-align: center;"><div style="display: inline-flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;" data-pagify="flagged-section-nav"></div></div>
                </div>
            </div>
        </div>
        
        <!-- Content Management Section -->
    <!-- Content Management Section -->
<div id="content" class="section-content">
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: var(--primary);">Dialogflow Intents & Guided Questions</h3>
            <div style="display: flex; gap: 10px;">
               
               
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="dashboard-cards" style="margin-bottom: 30px;">
            <div class="card stat-card">
                <h3>Total Intents</h3>
                <div class="value" id="totalIntents">0</div>
                <div class="trend">Active</div>
            </div>
            <div class="card stat-card">
                <h3>Guided Questions</h3>
                <div class="value" id="totalGuidedQuestions">0</div>
                <div class="trend">Configured</div>
            </div>
            <div class="card stat-card">
                <h3>Training Phrases</h3>
                <div class="value" id="totalTrainingPhrases">0</div>
                <div class="trend">Total</div>
            </div>
            <div class="card stat-card">
                <h3>Last Updated</h3>
                <div class="value" id="lastUpdatedTime">N/A</div>
                <div class="trend">Recently</div>
            </div>
        </div>

        <!-- Tabs for Intents and Guided Questions -->
        <div class="dashboard-tabs">
            <button class="dashboard-tab-btn active" data-tab="intents">Dialogflow Intents</button>
           
        </div>
        
        <!-- Intents Tab -->
        <div id="intents" class="dashboard-tab-content active">
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div class="search-box" style="flex: 1;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchIntents" placeholder="Search intents...">
                </div>
                <div style="display: flex; gap: 10px;">
                    <select id="intentFilter" style="padding: 10px 15px; border: 1px solid #e0efe5; border-radius: 8px; background: white;">
                        <option value="all">All Intents</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <button class="btn btn-secondary" onclick="syncWithDialogflow()">
                        <i class="fas fa-sync-alt"></i> Sync with Dialogflow
                    </button>
                </div>
            </div>
            
            <div class="data-table">
                <table id="intentsTable">
                    <thead>
                        <tr>
                            <th>Intent Name</th>
                            <th>Display Name</th>
                            <th>Training Phrases</th>
                            <th>Responses</th>
                            <th>Status</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="intentsTableBody">
                        <!-- Intents will be loaded here via JavaScript -->
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                Loading intents...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Guided Questions Tab -->
        <div id="guided-questions" class="dashboard-tab-content">
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div class="search-box" style="flex: 1;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchGuidedQuestions" placeholder="Search guided questions...">
                </div>
                <div style="display: flex; gap: 10px;">
                    <select id="questionFilter" style="padding: 10px 15px; border: 1px solid #e0efe5; border-radius: 8px; background: white;">
                        <option value="all">All Questions</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            
            <div class="data-table">
                <table id="guidedQuestionsTable">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Intent</th>
                            <th>Display Order</th>
                            <th>Response Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="guidedQuestionsTableBody">
                        <!-- Guided questions will be loaded here via JavaScript -->
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                Loading guided questions...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Unresolved Tickets Warning Modal -->
<div id="unresolvedTicketsModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header" style="background: #ffebee; border-bottom: 1px solid #ffcdd2;">
            <h3 style="color: #c62828; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle"></i> Cannot Archive Account
            </h3>
            <button class="close-modal" onclick="closeUnresolvedTicketsModal()">×</button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="fas fa-ticket-alt" style="font-size: 48px; color: #f44336; margin-bottom: 15px;"></i>
                <p style="font-size: 16px; color: #333; margin-bottom: 10px;">
                    This employee has <strong id="unresolvedTicketCount">0</strong> unresolved ticket(s).
                </p>
                <p style="font-size: 14px; color: #666;">
                    Please resolve all pending tickets before archiving this account.
                </p>
            </div>
            <div id="unresolvedTicketsList" style="max-height: 200px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #e0e0e0; border-radius: 8px; display: none;">
                <!-- Tickets will be populated here -->
            </div>
            <div style="display: flex; justify-content: center; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeUnresolvedTicketsModal()" style="padding: 10px 24px;">
                    Close
                </button>
                <button type="button" class="btn btn-primary" onclick="goToTicketsTab()" style="padding: 10px 24px; background: var(--primary);">
                    <i class="fas fa-ticket-alt"></i> View Tickets
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Intent Modal -->
<div id="intentModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 id="intentModalTitle">Create New Intent</h3>
            <button class="close-modal" onclick="closeIntentModal()">×</button>
        </div>
        <div class="modal-body">
            <form id="intentForm">
                @csrf
                <input type="hidden" id="intent_id" name="intent_id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="intent_name">Intent Name *</label>
                        <input type="text" id="intent_name" name="intent_name" required 
                               placeholder="e.g., leave.policy.inquiry">
                    </div>
                    
                    <div class="form-group">
                        <label for="display_name">Display Name *</label>
                        <input type="text" id="display_name" name="display_name" required 
                               placeholder="e.g., Leave Policy Inquiry">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Describe what this intent handles..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="training_phrases">Training Phrases * (One per line)</label>
                    <textarea id="training_phrases" name="training_phrases" rows="5" required 
                              placeholder="How do I apply for leave?
What are the leave policies?
How many leave days do I have?"></textarea>
                    <small style="color: #666;">Enter one training phrase per line</small>
                </div>
                
                <div class="form-group">
                    <label for="responses">Responses * (One per line)</label>
                    <textarea id="responses" name="responses" rows="5" required 
                              placeholder="You can apply for leave through the HR portal.
The leave policy allows for 20 days annual leave.
You can check your leave balance in the employee portal."></textarea>
                    <small style="color: #666;">Enter one response per line</small>
                </div>
                
                <div class="form-group">
                    <label for="parameters">Parameters (JSON format)</label>
                    <textarea id="parameters" name="parameters" rows="4" 
                              placeholder='{
  "leave_type": {
    "entity_type": "@sys.any",
    "mandatory": false,
    "prompts": ["What type of leave?"]
  }
}'></textarea>
                </div>
                
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority">
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label for="intentStatus">Status</label>
                        <select id="intentStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="submit" class="btn-primary">Save Intent</button>
                    <button type="button" class="btn-secondary" onclick="closeIntentModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create/Edit Guided Question Modal -->
<div id="guidedQuestionModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="guidedQuestionModalTitle">Add Guided Question</h3>
            <button class="close-modal" onclick="closeGuidedQuestionModal()">×</button>
        </div>
        <div class="modal-body">
            <form id="guidedQuestionForm">
                @csrf
                <input type="hidden" id="guided_question_id" name="guided_question_id">
                
                <div class="form-group">
                    <label for="question_text">Question Text *</label>
                    <textarea id="question_text" name="question_text" rows="3" required 
                              placeholder="Enter the guided question..."></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="linked_intent">Linked Intent *</label>
                        <select id="linked_intent" name="linked_intent" required>
                            <option value="">Select Intent</option>
                            <!-- Intents will be populated via JavaScript -->
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" min="1" value="1">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="response_type">Response Type</label>
                    <select id="response_type" name="response_type">
                        <option value="text">Text Response</option>
                        <option value="buttons">Buttons</option>
                        <option value="cards">Cards</option>
                        <option value="quick_replies">Quick Replies</option>
                    </select>
                </div>
                
                <div class="form-group" id="customResponseContainer" style="display: none;">
                    <label for="custom_response">Custom Response (JSON)</label>
                    <textarea id="custom_response" name="custom_response" rows="4" 
                              placeholder='{
  "type": "buttons",
  "buttons": [
    {"text": "Option 1", "value": "option1"},
    {"text": "Option 2", "value": "option2"}
  ]
}'></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" 
                               placeholder="e.g., HR Policies, Payroll, etc.">
                    </div>
                    
                    <div class="form-group">
                        <label for="status_gq">Status</label>
                        <select id="status_gq" name="status_gq">
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <button type="submit" class="btn-primary">Save Question</button>
                    <button type="button" class="btn-secondary" onclick="closeGuidedQuestionModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="close-modal" onclick="closeConfirmDeleteModal()">×</button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage">Are you sure you want to delete this item?</p>
            <div style="display: flex; gap: 10px; margin-top: 25px; justify-content: flex-end;">
                <button class="btn-danger" onclick="confirmDelete()">Delete</button>
                <button class="btn-secondary" onclick="closeConfirmDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

 <!-- Chatbot Ticket Details Section -->
        <div id="tickets" class="section-content">
            <div class="dashboard-cards" id="ticketKpiCards">
                <div class="card stat-card kpi-card active" data-kpi="all" onclick="filterTicketsByKPI('all', this)">
                    <h3>Total Tickets</h3>
                    <div class="value" id="totalTickets">{{ $totalTickets ?? 0 }}</div>
                </div>
                <div class="card stat-card kpi-card" data-kpi="unresolved" onclick="filterTicketsByKPI('unresolved', this)">
                    <h3>Unresolved Tickets</h3>
                    <div class="value" id="unresolvedTickets">{{ $unresolvedTickets ?? 0 }}</div>
                </div>
                <div class="card stat-card kpi-card" data-kpi="resolved" onclick="filterTicketsByKPI('resolved', this)">
                    <h3>Resolved Tickets</h3>
                    <div class="value" id="resolvedTickets">{{ $resolvedTickets ?? 0 }}</div>
                </div>
                <div class="card stat-card" style="cursor: default;">
                    <h3>Most Frequent Category</h3>
                    <div class="value" style="font-size: 1.2rem; color: var(--primary);">{{ \Illuminate\Support\Str::title($mostFrequentCategory ?? 'N/A') }}</div>
                    @if(isset($mostFrequentCategoryCount))
                    <div style="font-size: 0.85rem; color: #666; margin-top: 5px;">{{ $mostFrequentCategoryCount }} tickets</div>
                    @endif
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
                            <th class="sortable" onclick="sortTable('ticketsTable', 2, 'text')">Category</th>
                            <th class="sortable" onclick="sortTable('ticketsTable', 3, 'text')">Priority</th>
                            <th class="sortable" onclick="sortTable('ticketsTable', 4, 'text')">Status</th>
                            <th class="sortable" onclick="sortTable('ticketsTable', 5, 'date')">Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody">
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_no ?? $ticket->id }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($ticket->message ?? 'No message', 50) }}</td>
                            <td>
                                @php
                                    $category = $ticket->category ?? 'General';
                                    $words = explode(' ', strtolower($category));
                                    $formatted = array_map(function($word) {
                                        return strlen($word) >= 4 ? ucfirst($word) : $word;
                                    }, $words);
                                    echo implode(' ', $formatted);
                                @endphp
                            </td>
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
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                No tickets found in the system.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 20px;">
                    <div style="text-align: center; margin-bottom: 10px; color: #666; font-size: 0.9rem;" data-pagify="tickets-info"></div>
                    <div style="text-align: center;"><div style="display: inline-flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;" data-pagify="tickets-nav"></div></div>
                </div>
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
                <div id="importErrorPopup" class="modal active" style="display:block;">
                    <div class="modal-content" style="max-width: 500px;">
                        <div class="modal-header">
                            <h3 style="color: var(--danger);">Import Errors</h3>
                            <button class="close-modal" onclick="closeImportErrorPopup()">×</button>
                        </div>
                        <div class="modal-body">
                            <p style="color: #856404;">The following errors occurred during import:</p>
                            <ul style="margin: 5px 0 0 20px; padding: 0; color: #856404;">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <div style="margin-top: 20px; text-align: right;">
                                <button class="btn-secondary" onclick="closeImportErrorPopup()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                // Auto-open import modal if errors exist
                document.addEventListener('DOMContentLoaded', function() {
                    openImportModal();
                });
                function closeImportErrorPopup() {
                    var popup = document.getElementById('importErrorPopup');
                    if (popup) popup.style.display = 'none';
                }
                </script>
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
                            <td>
                                {{ $user->employeeNum }}
                                @if($user->is_online)
                                <span class="online-indicator" title="User is currently online" style="display: inline-block; width: 8px; height: 8px; background-color: #28a745; border-radius: 50%; margin-left: 5px; animation: pulse 1.5s infinite;"></span>
                                @endif
                            </td>
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
                                @if($user->is_online)
                                <span class="badge" style="background-color: #28a745; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; margin-left: 5px;">Online</span>
                                @endif
                            </td>
                            <td class="action-buttons-cell">
                                <button class="btn-action btn-view" onclick="viewAccount('{{ $user->employeeNum }}')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @if($user->role != 'Admin')
                                <button class="btn-action btn-edit" onclick="editAccountModal('{{ $user->employeeNum }}')" @if($user->is_online && $user->employeeNum != Auth::user()->employeeNum) title="Cannot edit while user is online" @endif>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-action btn-reset" onclick="resetPasswordModal('{{ $user->employeeNum }}')">
                                    <i class="fas fa-key"></i> Reset
                                </button>
                                @endif
                                @if($user->employeeNum != Auth::user()->employeeNum && $user->role != 'Admin')
                                <button class="btn-action btn-archive" onclick="archiveAccount('{{ $user->employeeNum }}')" @if($user->is_online) disabled title="Cannot archive while user is online" style="opacity: 0.5; cursor: not-allowed;" @endif>
                                    <i class="fas fa-archive"></i> Archive
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

        <!-- Account Settings Section -->
        <div id="account-settings" class="section-content">
            <div style="width: 100%; max-width: 1000px; margin: 0 auto;">
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
    </div> <!-- End main-content -->

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
                                <option value="HR" {{ old('role') == 'HR' ? 'selected' : '' }}>Human Resources</option>
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
                            <label for="accountStatus">Account Status *</label>
                            <select id="accountStatus" name="status" required>
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
                                <option value="HR">Human Resources</option>
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
        
        // DEBUG: Log data count on load
        console.log('=== INTERACTIONS DATA DEBUG ===');
        console.log('allInteractionsData loaded, count:', allInteractionsData.length);
        if (allInteractionsData.length > 0) {
            console.log('First item sample:', JSON.stringify(allInteractionsData[0]));
            console.log('Last item sample:', JSON.stringify(allInteractionsData[allInteractionsData.length - 1]));
        }

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

        document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    console.log('Available tables:', {
        intentsTable: !!document.getElementById('intentsTableBody'),
        guidedQuestionsTable: !!document.getElementById('guidedQuestionsTableBody')
    });
    
    // Initialize content management tabs
    initContentManagementTabs();
    
    // Load intents and guided questions
    loadIntents();
    loadGuidedQuestions();
    
    // Rest of your code...
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
        // UNIFIED FILTER STATE SYSTEM
        // =============================================
        // Central filter state - all filters read from and write to this
        const filterState = {
            dateRange: {
                type: 'overall',
                start: null,
                end: null,
                label: 'Overall'
            },
            starRating: {
                feedback: 'all',      // For feedback tab (dashboard)
                feedbackSection: 'all' // For feedback section
            }
        };

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
                    startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0, 0);
                    endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59, 999);
                    displayText = formatDate(startDate);
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'Today'
                    };
                    break;
                    
                case 'weekly':
                    const firstDayOfWeek = today.getDate() - today.getDay();
                    startDate = new Date(today.getFullYear(), today.getMonth(), firstDayOfWeek, 0, 0, 0, 0);
                    endDate = new Date(today.getFullYear(), today.getMonth(), firstDayOfWeek + 6, 23, 59, 59, 999);
                    displayText = formatDate(startDate) + ' - ' + formatDate(endDate);
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'This Week'
                    };
                    break;
                    
                case 'monthly':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1, 0, 0, 0, 0);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0, 23, 59, 59, 999);
                    displayText = startDate.toLocaleString('default', { month: 'long', year: 'numeric' });
                    dateRangeData = {
                        start: startDate,
                        end: endDate,
                        label: 'This Month'
                    };
                    break;
                    
                case 'annually':
                    startDate = new Date(today.getFullYear(), 0, 1, 0, 0, 0, 0);
                    endDate = new Date(today.getFullYear(), 11, 31, 23, 59, 59, 999);
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
        }

        // Filter tickets by date range - now delegates to combined filter
        function filterTicketsByDate(startDate, endDate) {
            // The date range is already stored in dateRangeData by applyDateRangeFilter
            // Just call the combined filter which reads from dateRangeData
            if (typeof applyCombinedTicketFilters === 'function') {
                applyCombinedTicketFilters();
            }
        }

        // =============================================
        // UNIFIED FEEDBACK FILTER (chains date + star filters)
        // =============================================
        function applyAllFeedbackFilters(tableId, starFilterKey) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const feedbackRows = table.querySelectorAll('tbody tr');
            const { start: startDate, end: endDate } = dateRangeData;
            const starFilter = filterState.starRating[starFilterKey];
            
            let visibleCount = 0;
            let totalRating = 0;
            const isFiltered = (startDate !== null || endDate !== null) || starFilter !== 'all';
            
            feedbackRows.forEach(row => {
                // Skip empty state rows (with colspan)
                if (row.querySelector('td[colspan]')) {
                    row.style.display = 'none';
                    return;
                }
                
                let passesDateFilter = true;
                let passesStarFilter = true;
                
                // Check date filter
                const dateCell = row.cells[3];
                if (dateCell && dateCell.textContent.trim()) {
                    const rowDate = parseDateFromCell(dateCell.textContent);
                    if (startDate !== null && endDate !== null) {
                        passesDateFilter = (rowDate >= startDate && rowDate <= endDate);
                    }
                }
                
                // Check star filter
                const ratingCell = row.cells[1];
                let rating = null;
                if (ratingCell) {
                    const ratingMatch = ratingCell.textContent.match(/\((\d)\/5\)/);
                    if (ratingMatch) {
                        rating = ratingMatch[1];
                        if (starFilter !== 'all') {
                            passesStarFilter = (rating === starFilter);
                        }
                    }
                }
                
                // Row is visible only if it passes ALL filters
                if (passesDateFilter && passesStarFilter) {
                    row.style.display = '';
                    visibleCount++;
                    if (rating) totalRating += parseInt(rating);
                } else {
                    row.style.display = 'none';
                }
            });
            
            return { visibleCount, totalRating, isFiltered };
        }

        // Filter feedback by date range (now uses unified filter)
        function filterFeedbackByDate(startDate, endDate) {
            // Update dateRangeData (already done by caller, but ensure consistency)
            // Filter dashboard feedback table
            const dashResult = applyAllFeedbackFilters('feedbackDashTable', 'feedback');
            if (dashResult) {
                const avgRating = dashResult.visibleCount > 0 ? (dashResult.totalRating / dashResult.visibleCount).toFixed(2) : 'N/A';
                if (document.getElementById('avgFeedbackValue')) {
                    document.getElementById('avgFeedbackValue').textContent = avgRating;
                    document.getElementById('avgFeedbackCount').textContent = `Based on ${dashResult.visibleCount} feedback${dashResult.visibleCount === 1 ? '' : 's'}`;
                }
                updatePagificationDisplay('feedback', dashResult.visibleCount, dashResult.isFiltered);
            }
            
            // Filter feedback section table
            const sectionResult = applyAllFeedbackFilters('feedbackSectionTable', 'feedbackSection');
            if (sectionResult) {
                const avgRating = sectionResult.visibleCount > 0 ? (sectionResult.totalRating / sectionResult.visibleCount).toFixed(2) : 'N/A';
                if (document.getElementById('avgFeedbackValueSection')) {
                    document.getElementById('avgFeedbackValueSection').textContent = avgRating;
                    document.getElementById('avgFeedbackCountSection').textContent = `Based on ${sectionResult.visibleCount} feedback${sectionResult.visibleCount === 1 ? '' : 's'}`;
                }
                updatePagificationDisplay('feedback-section', sectionResult.visibleCount, sectionResult.isFiltered);
            }
        }

        // Ensure pagification is updated on load and tab switch
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize client-side pagination for all tables
            setTimeout(function() {
                paginateTable('feedback');
                paginateTable('feedback-section');
                paginateTable('interactions');
                paginateTable('flagged-dash');
                paginateTable('flagged-section');
                paginateTable('tickets');
            }, 200);
        });

        // Also update pagification after tab switch (if using tabs)
        document.querySelectorAll('.dashboard-tab-btn, [data-tab]').forEach(btn => {
            btn.addEventListener('click', function() {
                setTimeout(function() {
                    // Re-paginate relevant tables after tab switch
                    paginateTable('feedback');
                    paginateTable('feedback-section');
                }, 200);
            });
        });

        // ========== CLIENT-SIDE PAGINATION SYSTEM ==========
        const paginationState = {
            feedback: { currentPage: 1, perPage: 20 },
            'feedback-section': { currentPage: 1, perPage: 20 },
            interactions: { currentPage: 1, perPage: 20 },
            'flagged-dash': { currentPage: 1, perPage: 20 },
            'flagged-section': { currentPage: 1, perPage: 20 },
            tickets: { currentPage: 1, perPage: 20 }
        };

        function paginateTable(type) {
            const state = paginationState[type];
            let tableSelector = '';
            
            switch(type) {
                case 'feedback': tableSelector = '#feedbackDashTable tbody tr:not([colspan])'; break;
                case 'feedback-section': tableSelector = '#feedbackSectionTable tbody tr:not([colspan])'; break;
                case 'interactions': tableSelector = '#interactionsTable tbody tr:not([colspan])'; break;
                case 'flagged-dash': tableSelector = '#flaggedDashTable tbody tr:not([colspan])'; break;
                case 'flagged-section': tableSelector = '#flaggedSectionTable tbody tr:not([colspan])'; break;
                case 'tickets': tableSelector = '#ticketsTable tbody tr:not([colspan])'; break;
            }
            
            const allRows = document.querySelectorAll(tableSelector);
            
            // Get only visible rows (not filtered out)
            const visibleRows = Array.from(allRows).filter(row => row.style.display !== 'none');
            const totalVisible = visibleRows.length;
            const totalPages = Math.ceil(totalVisible / state.perPage);
            
            // Ensure current page is valid
            if (state.currentPage > totalPages && totalPages > 0) {
                state.currentPage = totalPages;
            }
            if (state.currentPage < 1) {
                state.currentPage = 1;
            }
            
            // Hide all rows first
            allRows.forEach(row => {
                if (row.style.display !== 'none') { // Don't change filtered rows
                    row.classList.add('paginated-hidden');
                    row.style.visibility = 'hidden';
                    row.style.position = 'absolute';
                }
            });
            
            // Show only current page rows
            const startIdx = (state.currentPage - 1) * state.perPage;
            const endIdx = Math.min(startIdx + state.perPage, totalVisible);
            
            for (let i = startIdx; i < endIdx; i++) {
                visibleRows[i].classList.remove('paginated-hidden');
                visibleRows[i].style.visibility = '';
                visibleRows[i].style.position = '';
            }
            
            // Update pagination UI
            updatePaginationUI(type, totalVisible, startIdx, endIdx, totalPages);
        }

        function updatePaginationUI(type, totalVisible, startIdx, endIdx, totalPages) {
            const state = paginationState[type];
            let infoSelector = '';
            let navSelector = '';
            
            switch(type) {
                case 'feedback':
                    infoSelector = '[data-pagify="feedback-info"]';
                    navSelector = '[data-pagify="feedback-nav"]';
                    break;
                case 'feedback-section':
                    infoSelector = '[data-pagify="feedback-section-info"]';
                    navSelector = '[data-pagify="feedback-section-nav"]';
                    break;
                case 'interactions':
                    infoSelector = '[data-pagify="interactions-info"]';
                    navSelector = '[data-pagify="interactions-nav"]';
                    break;
                case 'flagged-dash':
                    infoSelector = '[data-pagify="flagged-dash-info"]';
                    navSelector = '[data-pagify="flagged-dash-nav"]';
                    break;
                case 'flagged-section':
                    infoSelector = '[data-pagify="flagged-section-info"]';
                    navSelector = '[data-pagify="flagged-section-nav"]';
                    break;
                case 'tickets':
                    infoSelector = '[data-pagify="tickets-info"]';
                    navSelector = '[data-pagify="tickets-nav"]';
                    break;
            }
            
            const info = document.querySelectorAll(infoSelector);
            const nav = document.querySelectorAll(navSelector);
            
            // Update info text
            info.forEach(el => {
                if (totalVisible === 0) {
                    el.textContent = '';  // Clear the text when no results
                    el.style.display = 'none';  // Hide the info div
                } else {
                    el.textContent = `Showing ${startIdx + 1} to ${endIdx} of ${totalVisible} result${totalVisible === 1 ? '' : 's'}`;
                    el.style.display = '';  // Show the info div
                }
            });
            
            // Update navigation
            nav.forEach(el => {
                if (totalPages <= 1) {
                    el.style.display = 'none';
                } else {
                    el.style.display = '';
                    renderPaginationButtons(el, type, totalPages);
                }
            });
        }

        function renderPaginationButtons(container, type, totalPages) {
            const state = paginationState[type];
            const currentPage = state.currentPage;
            
            let html = '';
            
            // Previous button
            if (currentPage > 1) {
                html += `<button onclick="goToPage('${type}', ${currentPage - 1})" style="padding: 8px 12px; background: white; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); cursor: pointer;">\u00ab Previous</button>`;
            } else {
                html += `<span style="padding: 8px 12px; color: #ccc;">\u00ab Previous</span>`;
            }
            
            // First page
            if (currentPage > 3) {
                html += `<button onclick="goToPage('${type}', 1)" style="padding: 6px 10px; background: white; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); cursor: pointer;">1</button>`;
                if (currentPage > 4) {
                    html += `<span style="padding: 6px 10px; color: #666;">...</span>`;
                }
            }
            
            // Page numbers around current page
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                if (i === currentPage) {
                    html += `<span style="padding: 6px 10px; background: var(--secondary); border: 1px solid var(--secondary); border-radius: 4px; color: white; font-weight: bold;">${i}</span>`;
                } else {
                    html += `<button onclick="goToPage('${type}', ${i})" style="padding: 6px 10px; background: white; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); cursor: pointer;">${i}</button>`;
                }
            }
            
            // Last page
            if (currentPage < totalPages - 2) {
                if (currentPage < totalPages - 3) {
                    html += `<span style="padding: 6px 10px; color: #666;">...</span>`;
                }
                html += `<button onclick="goToPage('${type}', ${totalPages})" style="padding: 6px 10px; background: white; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); cursor: pointer;">${totalPages}</button>`;
            }
            
            // Next button
            if (currentPage < totalPages) {
                html += `<button onclick="goToPage('${type}', ${currentPage + 1})" style="padding: 8px 12px; background: white; border: 1px solid #e0efe5; border-radius: 4px; color: var(--primary); cursor: pointer;">Next \u00bb</button>`;
            } else {
                html += `<span style="padding: 8px 12px; color: #ccc;">Next \u00bb</span>`;
            }
            
            container.innerHTML = html;
        }

        function goToPage(type, page) {
            paginationState[type].currentPage = page;
            paginateTable(type);
        }

        function refreshPagination(type) {
            if (!paginationState[type]) {
                console.warn('Pagination state not found for type:', type);
                return;
            }
            paginationState[type].currentPage = 1; // Reset to first page
            paginateTable(type);
        }

        // Backward compatibility wrapper - calls new pagination system
        function updatePagificationDisplay(type, visibleCount, isFiltered = true) {
            refreshPagination(type);
        }

        // Filter interactions by date range
        function filterInteractionsByDate(startDate, endDate) {
            const interactionRows = document.querySelectorAll('#interactionsTable tbody tr');
            let visibleCount = 0;
            const isFiltered = startDate !== null || endDate !== null;
            
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
                    visibleCount++;
                } else if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            updatePagificationDisplay('interactions', visibleCount, isFiltered);
        }

        // Filter flagged responses by date range
        function filterFlaggedByDate(startDate, endDate) {
            // Select flagged responses from both dashboard feedback tab and feedback section
            const flaggedTables = document.querySelectorAll('#feedback .data-table:last-child tbody tr, #feedback-section .data-table:last-child tbody tr');
            let visibleCount = 0;
            const isFiltered = startDate !== null || endDate !== null;
            
            flaggedTables.forEach(row => {
                const dateCell = row.querySelector('td:nth-child(4)'); // Date column is 4th for flagged (after removing User column)
                if (!dateCell || !dateCell.textContent.trim() || row.querySelector('td[colspan]')) {
                    return;
                }
                
                const rowDate = parseDateFromCell(dateCell.textContent);
                
                if (startDate === null && endDate === null) {
                    row.style.display = '';
                    visibleCount++;
                } else if (rowDate >= startDate && rowDate <= endDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            updatePagificationDisplay('flagged', visibleCount, isFiltered);
            
            // Update flagged reason KPI after filtering
            if (typeof updateFlaggedReasonKPI === 'function') updateFlaggedReasonKPI();
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
            // NOTE: Ticket KPIs are now handled by applyCombinedTicketFilters()
            // which respects ALL active filters (date, priority, status, KPI, search)
            // This function only updates ticket trends and dashboard KPIs
            
            // Update ticket trends
            updateTicketTrends();
            
            // Update dashboard KPIs and chart
            updateDashboardKPIs();
        }
        
        // Update ticket trends based on date range filter
        function updateTicketTrends() {
            const { start, end } = dateRangeData;
            
            console.log('updateTicketTrends called', { start, end, currentDateRange });
            
            const totalTrend = document.getElementById('totalTicketsTrend');
            const unresolvedTrend = document.getElementById('unresolvedTicketsTrend');
            const resolvedTrend = document.getElementById('resolvedTicketsTrend');
            
            // Check if elements exist
            if (!totalTrend || !unresolvedTrend || !resolvedTrend) {
                console.log('Trend elements not found, skipping update');
                return;
            }
            
            // Hide trends for overall view
            if (start === null || end === null || currentDateRange === 'overall') {
                totalTrend.style.display = 'none';
                unresolvedTrend.style.display = 'none';
                resolvedTrend.style.display = 'none';
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
            
            // Calculate average - only from items with valid response time
            let totalResponseTime = 0;
            let count = 0;
            
            filteredData.forEach(interaction => {
                // Only count items with valid response time (responseTime != questionTime)
                if (interaction.has_valid_response_time == 1) {
                    const responseTime = parseFloat(interaction.response_time_seconds);
                    if (!isNaN(responseTime)) {
                        totalResponseTime += responseTime;
                        count++;
                    }
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
                        data: [Math.max(0, resolved), Math.max(0, pending), Math.max(0, escalated)],
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
                            min: 0,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                callback: function(value) {
                                    return value < 0 ? 0 : value;
                                }
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
        function fetchFilteredKPIs(range, topic = null) {
            let url = `/admin/kpis/filter?range=${range}`;
            if (topic) {
                url += `&topic=${encodeURIComponent(topic)}`;
            }
            fetch(url)
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
                        // Update Flagged Count KPI
                        if (document.getElementById('commonFlaggedReasonCountSection')) {
                            document.getElementById('commonFlaggedReasonCountSection').textContent = `Based on ${data.flaggedCount} flag${data.flaggedCount === 1 ? '' : 's'}`;
                        }
                        // Update Most Asked Topics
                        updateMostAskedTopics(data.mostAskedTopics);
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

        // Update Most Asked Topics Display
        function updateMostAskedTopics(topics) {
            const topicsContainer = document.querySelector('.topics-container');
            if (!topicsContainer) return;
            
            // Keep the heading
            const heading = topicsContainer.querySelector('h3');
            topicsContainer.innerHTML = '';
            if (heading) topicsContainer.appendChild(heading);
            
            if (topics && topics.length > 0) {
                topics.forEach(topic => {
                    const topicItem = document.createElement('div');
                    topicItem.className = 'topic-item';
                    topicItem.style.cursor = 'pointer';
                    topicItem.style.transition = 'all 0.2s';
                    topicItem.dataset.topic = topic.topic;
                    topicItem.onclick = () => filterByTopic(topic.topic);
                    topicItem.onmouseover = function() {
                        if (!this.dataset.active) this.style.background = '#f0f8f4';
                    };
                    topicItem.onmouseout = function() {
                        if (!this.dataset.active) this.style.background = 'transparent';
                    };
                    
                    // Apply selective title case (only words with 4+ characters)
                    const titleCaseTopic = topic.topic.toLowerCase().split(' ').map(word => {
                        return word.length >= 4 ? word.charAt(0).toUpperCase() + word.slice(1) : word;
                    }).join(' ');
                    
                    topicItem.innerHTML = `
                        <span class="topic-name">${titleCaseTopic}</span>
                        <span class="topic-count">${topic.count} inquir${topic.count == 1 ? 'y' : 'ies'}</span>
                    `;
                    topicsContainer.appendChild(topicItem);
                });
            } else {
                const noDataItem = document.createElement('div');
                noDataItem.className = 'topic-item';
                noDataItem.innerHTML = `
                    <span class="topic-name" style="color: #666; font-style: italic;">No data available yet</span>
                    <span class="topic-count">0 inquiries</span>
                `;
                topicsContainer.appendChild(noDataItem);
            }
        }

        // Filter by topic
        let currentTopicFilter = null;
        function filterByTopic(topic) {
            console.log('Filtering by topic:', topic);
            
            // Toggle filter if same topic clicked
            if (currentTopicFilter === topic) {
                currentTopicFilter = null;
                showNotification('Topic filter removed', 'success');
            } else {
                currentTopicFilter = topic;
                showNotification(`Filtering by: ${topic}`, 'success');
            }
            
            // Update visual state
            document.querySelectorAll('.topic-item').forEach(item => {
                if (currentTopicFilter && item.dataset.topic === currentTopicFilter) {
                    item.style.background = '#d4edda';
                    item.style.borderLeft = '3px solid var(--primary)';
                    item.dataset.active = 'true';
                } else {
                    item.style.background = 'transparent';
                    item.style.borderLeft = 'none';
                    delete item.dataset.active;
                }
            });
            
            // Fetch filtered data
            fetchFilteredKPIs(currentDateRange, currentTopicFilter);
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
            
            // Tickets table - columns: 0=Ticket#, 1=Message, 2=Category, 3=Priority, 4=Status, 5=Date, 6=Action
            const ticketsSort = urlParams.get('tickets_sort') || 'created_at';
            const ticketsDir = urlParams.get('tickets_dir') || 'desc';
            markSortedColumn('ticketsTable', ['ticket_no', null, 'category', 'priority', 'status', 'created_at', null], ticketsSort, ticketsDir);
            
            // Feedback table (both dashboard and section) - columns: 0=ID, 1=Rating, 2=Subject, 3=Date, 4=Actions
            const feedbackSort = urlParams.get('feedback_sort') || 'timeStamp';
            const feedbackDir = urlParams.get('feedback_dir') || 'desc';
            markSortedColumn('feedbackDashTable', ['feedbackID', 'rating', null, 'timeStamp', null], feedbackSort, feedbackDir);
            markSortedColumn('feedbackSectionTable', ['feedbackID', 'rating', null, 'timeStamp', null], feedbackSort, feedbackDir);
            
            // Flags table (all instances) - columns: 0=ID, 1=Query, 2=Reason, 3=Date, 4=Status, 5=Action
            const flagsSort = urlParams.get('flags_sort') || 'timeStamp';
            const flagsDir = urlParams.get('flags_dir') || 'desc';
            markSortedColumn('flaggedTable', ['flaggedID', null, null, 'timeStamp', 'status', null], flagsSort, flagsDir);
            markSortedColumn('flaggedDashTable', ['flaggedID', null, null, 'timeStamp', 'status', null], flagsSort, flagsDir);
            markSortedColumn('flaggedSectionTable', ['flaggedID', null, null, 'timeStamp', 'status', null], flagsSort, flagsDir);
        });

        function markSortedColumn(tableId, columns, sortColumn, sortDir) {
            const table = document.getElementById(tableId);
            if (!table) return;
            // Ensure flagged reason KPI is always updated after all filtering
            if (typeof updateFlaggedReasonKPI === 'function') updateFlaggedReasonKPI();
            
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
            
            // Initialize performance charts
            initPerformanceCharts();
        }

        // =============================================
        // CHATBOT PERFORMANCE CHARTS
        // =============================================
        let responseTimeTrendChartInstance = null;
        let interactionsOverTimeChartInstance = null;
        
        // Initialize performance section charts
        function initPerformanceCharts() {
            // Set default date range (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            document.getElementById('performanceStartDate').value = thirtyDaysAgo.toISOString().split('T')[0];
            document.getElementById('performanceEndDate').value = today.toISOString().split('T')[0];
            
            // Apply initial filter
            applyPerformanceDateFilter();
        }
        
        // Apply date filter for performance section
        function applyPerformanceDateFilter() {
            const startDate = document.getElementById('performanceStartDate').value;
            const endDate = document.getElementById('performanceEndDate').value;
            
            console.log('Performance Filter - Start:', startDate, 'End:', endDate);
            console.log('Total allInteractionsData count:', allInteractionsData.length);
            
            // Filter interaction data by date range
            let filteredData = allInteractionsData;
            if (startDate && endDate) {
                filteredData = allInteractionsData.filter(item => {
                    // Handle null/undefined query_date
                    if (!item.query_date) return false;
                    const itemDate = String(item.query_date);
                    return itemDate >= startDate && itemDate <= endDate;
                });
            }
            
            console.log('Filtered data count:', filteredData.length);
            
            // Update KPIs
            updatePerformanceKPIs(filteredData, startDate, endDate);
            
            // Update charts
            updatePerformanceCharts(filteredData);
        }
        
        // Reset date filter
        function resetPerformanceDateFilter() {
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            document.getElementById('performanceStartDate').value = thirtyDaysAgo.toISOString().split('T')[0];
            document.getElementById('performanceEndDate').value = today.toISOString().split('T')[0];
            
            applyPerformanceDateFilter();
        }
        
        // Update KPIs based on filtered data
        function updatePerformanceKPIs(data, startDate, endDate) {
            // Total interactions
            const totalInteractions = data.length;
            document.getElementById('performanceTotalInteractions').textContent = totalInteractions.toLocaleString();
            
            // Average response time - only count items with valid response time
            // (has_valid_response_time = 1 means responseTime != questionTime)
            const validResponseItems = data.filter(item => item.has_valid_response_time == 1);
            let avgResponseTime = 0;
            if (validResponseItems.length > 0) {
                const totalTime = validResponseItems.reduce((sum, item) => sum + (parseFloat(item.response_time_seconds) || 0), 0);
                avgResponseTime = totalTime / validResponseItems.length;
            }
            document.getElementById('performanceAvgResponseTime').textContent = avgResponseTime.toFixed(2) + 's';
            
            // Update date range labels
            const rangeText = startDate && endDate ? 
                `${formatDateShort(startDate)} - ${formatDateShort(endDate)}` : 'All time';
            document.getElementById('performanceInteractionsRange').textContent = rangeText;
            document.getElementById('performanceResponseTimeRange').textContent = rangeText + ` (${validResponseItems.length} with timing data)`;
        }
        
        // Format date for display
        function formatDateShort(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        
        // Update performance charts
        function updatePerformanceCharts(data) {
            // Group data by date
            const dateGroups = {};
            data.forEach(item => {
                const date = item.query_date;
                if (!dateGroups[date]) {
                    dateGroups[date] = { count: 0, totalTime: 0, validTimeCount: 0 };
                }
                dateGroups[date].count++;
                // Only add response time if it's a valid measurement
                if (item.has_valid_response_time == 1) {
                    dateGroups[date].totalTime += parseFloat(item.response_time_seconds) || 0;
                    dateGroups[date].validTimeCount++;
                }
            });
            
            // Sort dates
            const sortedDates = Object.keys(dateGroups).sort();
            
            // Prepare chart data
            const labels = sortedDates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            
            const responseTimeData = sortedDates.map(date => {
                const group = dateGroups[date];
                // Only calculate average from valid response times
                return group.validTimeCount > 0 ? (group.totalTime / group.validTimeCount).toFixed(2) : 0;
            });
            
            const interactionData = sortedDates.map(date => dateGroups[date].count);
            
            // Update Response Time Trend Chart
            const responseTimeCanvas = document.getElementById('responseTimeTrendChart');
            if (responseTimeCanvas) {
                const ctx = responseTimeCanvas.getContext('2d');
                
                if (responseTimeTrendChartInstance) {
                    responseTimeTrendChartInstance.destroy();
                }
                
                responseTimeTrendChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Avg Response Time (seconds)',
                            data: responseTimeData,
                            borderColor: '#1565c0',
                            backgroundColor: 'rgba(21, 101, 192, 0.15)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#1565c0',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Avg Response Time: ' + context.parsed.y + 's';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Seconds'
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }
            
            // Update Interactions Over Time Chart
            const interactionsCanvas = document.getElementById('interactionsOverTimeChart');
            if (interactionsCanvas) {
                const ctx = interactionsCanvas.getContext('2d');
                
                if (interactionsOverTimeChartInstance) {
                    interactionsOverTimeChartInstance.destroy();
                }
                
                interactionsOverTimeChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Interactions',
                            data: interactionData,
                            borderColor: '#2d5a3d',
                            backgroundColor: 'rgba(45, 90, 61, 0.15)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#2d5a3d',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Interactions: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Count'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }
        }

        // =============================================
        // CHATBOT TICKETS FUNCTIONALITY
        // =============================================
        let currentTicketId = null;
        let ticketPriorityChartInstance = null;
        let ticketStatusChartInstance = null;

        // Initialize ticket charts
        function initTicketCharts() {
            // Count from VISIBLE rows in the table (respects ALL active filters)
            const visibleRows = document.querySelectorAll('#ticketsTableBody tr:not([style*="display: none"])');
            
            // Count by priority from visible rows
            const priorityCounts = {};
            visibleRows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty state
                const priorityEl = row.querySelector('.priority');
                if (priorityEl) {
                    const priority = priorityEl.textContent.trim().toLowerCase();
                    priorityCounts[priority] = (priorityCounts[priority] || 0) + 1;
                }
            });
            
            // Count by status from visible rows
            const statusCounts = {};
            visibleRows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty state
                const statusEl = row.querySelector('.status');
                if (statusEl) {
                    const status = statusEl.textContent.trim();
                    statusCounts[status] = (statusCounts[status] || 0) + 1;
                }
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
            // Delegate to the combined filter to ensure all filters chain properly
            if (typeof applyCombinedTicketFilters === 'function') {
                applyCombinedTicketFilters();
            }
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
                        let ticketContent = `
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
                                    <div class="value">${ticket.category ? ticket.category.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ') : 'N/A'}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>Intent:</label>
                                    <div class="value">${ticket.intent || 'N/A'}</div>
                                </div>
                                <div class="ticket-detail-item">
                                    <label>${ticket.status === 'Resolved' ? 'Resolved By' : 'Assigned To'}:</label>
                                    <div class="value">${ticket.status === 'Resolved' ? (ticket.resolved_by || 'N/A') : (ticket.assigned_to || 'Unassigned')}</div>
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

        // ARCHIVE ACCOUNT FUNCTION
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

        function archiveAccount(employeeNum) {
            // Check for unresolved tickets via AJAX
            fetch(`/admin/accounts/${employeeNum}/unresolved-tickets`, {
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.hasUnresolved) {
                    // Show the unresolved tickets modal
                    showUnresolvedTicketsModal(data.unresolvedCount, data.tickets);
                } else {
                    if (confirm(`Are you sure you want to archive account ${employeeNum}? The account will be archived and deactivated but data will be preserved.`)) {
                        // Use AJAX instead of form submission for seamless UX
                        fetch(`/admin/accounts/${employeeNum}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                showNotification(result.message || 'Account archived successfully!', 'success');
                                // Reload page to refresh the table
                                setTimeout(() => {
                                    window.location.href = window.location.pathname + '?active_tab=account-management';
                                }, 1000);
                            } else {
                                showNotification(result.message || 'Failed to archive account', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error archiving account:', error);
                            showNotification('An error occurred while archiving the account.', 'error');
                        });
                    }
                }
            })
            .catch(() => {
                showNotification('Could not check unresolved tickets. Please try again.', 'error');
            });
        }

        // Show Unresolved Tickets Modal
        function showUnresolvedTicketsModal(count, tickets) {
            const modal = document.getElementById('unresolvedTicketsModal');
            const countElement = document.getElementById('unresolvedTicketCount');
            const listElement = document.getElementById('unresolvedTicketsList');
            
            countElement.textContent = count;
            
            // Populate tickets list if available
            if (tickets && tickets.length > 0) {
                listElement.style.display = 'block';
                listElement.innerHTML = tickets.map(ticket => `
                    <div style="padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #333;">#${ticket.ticket_no}</strong>
                            <span style="font-size: 12px; color: #666; margin-left: 10px;">${new Date(ticket.created_at).toLocaleDateString()}</span>
                        </div>
                        <span class="status-badge" style="padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; 
                            ${ticket.status === 'Open' ? 'background: #fff3e0; color: #e65100;' : 
                              ticket.status === 'Replied' ? 'background: #e3f2fd; color: #1565c0;' : 
                              'background: #fce4ec; color: #c62828;'}">
                            ${ticket.status}
                        </span>
                    </div>
                `).join('');
            } else {
                listElement.style.display = 'none';
            }
            
            modal.classList.add('active');
        }

        // Close Unresolved Tickets Modal
        function closeUnresolvedTicketsModal() {
            document.getElementById('unresolvedTicketsModal').classList.remove('active');
        }

        // Navigate to Tickets Tab
        function goToTicketsTab() {
            closeUnresolvedTicketsModal();
            // Switch to tickets tab
            const ticketsLink = document.querySelector('a[data-section="tickets"]');
            if (ticketsLink) {
                ticketsLink.click();
            }
        }

        // =============================================
        // TABLE SORTING FUNCTIONALITY
        // =============================================
        function sortTable(tableId, columnIndex, dataType) {
            // Map table IDs to their sort parameter names and column mappings
            // Each columns array index matches the table column index (0-based)
            const sortConfig = {
                'ticketsTable': {
                    param: 'tickets',
                    // Columns: 0=Ticket#, 1=Message, 2=Category, 3=Priority, 4=Status, 5=Date, 6=Action
                    columns: ['ticket_no', null, 'category', 'priority', 'status', 'created_at', null]
                },
                'feedbackDashTable': {
                    param: 'feedback',
                    // Columns: 0=Feedback ID, 1=Rating, 2=Subject, 3=Date, 4=Actions
                    columns: ['feedbackID', 'rating', null, 'timeStamp', null]
                },
                'feedbackSectionTable': {
                    param: 'feedback',
                    // Columns: 0=Feedback ID, 1=Rating, 2=Subject, 3=Date, 4=Actions
                    columns: ['feedbackID', 'rating', null, 'timeStamp', null]
                },
                'interactionsTable': {
                    param: 'interactions',
                    // Columns: 0=Query, 1=Response Time, 2=Status, 3=Date
                    columns: ['question', 'response_time_seconds', 'isEscalated', 'questionTime']
                },
                'flaggedTable': {
                    param: 'flags',
                    // Columns: 0=Flag ID, 1=Query, 2=Reason, 3=Date, 4=Status, 5=Action
                    columns: ['flaggedID', null, null, 'timeStamp', 'status', null]
                },
                'flaggedDashTable': {
                    param: 'flags',
                    // Columns: 0=Flag ID, 1=Query, 2=Reason, 3=Date, 4=Status, 5=Action
                    columns: ['flaggedID', null, null, 'timeStamp', 'status', null]
                },
                'flaggedSectionTable': {
                    param: 'flags',
                    // Columns: 0=Flag ID, 1=Query, 2=Reason, 3=Date, 4=Status, 5=Action
                    columns: ['flaggedID', null, null, 'timeStamp', 'status', null]
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
        let feedbackRatingChartSectionInstance = null;
        let flaggedReasonChartSectionInstance = null;

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

            // Create Feedback Rating Chart (Dashboard)
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

            // Create Flagged Reason Chart (Dashboard)
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

            // Create charts for Feedback Section as well
            initFeedbackSectionCharts();
        }

        function initFeedbackSectionCharts() {
            // Get feedback rating data from feedback section table (only visible rows)
            const feedbackRows = document.querySelectorAll('#feedbackSectionTable tbody tr:not([colspan])');
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

            // Create Feedback Rating Chart (Section)
            const ratingCanvas = document.getElementById('feedbackRatingChartSection');
            if (ratingCanvas) {
                const ctx = ratingCanvas.getContext('2d');
                
                if (feedbackRatingChartSectionInstance) {
                    feedbackRatingChartSectionInstance.destroy();
                }
                
                feedbackRatingChartSectionInstance = new Chart(ctx, {
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

            // Get flagged responses reason data from feedback section table (only visible rows)
            const flaggedRows = document.querySelectorAll('#flaggedSectionTable tbody tr:not([colspan])');
            const reasonCounts = {};
            
            flaggedRows.forEach(row => {
                // Skip hidden rows (filtered out by date range)
                if (row.style.display === 'none') return;
                
                const reason = row.cells[2]?.textContent.trim();
                if (reason && reason !== 'Unknown') {
                    reasonCounts[reason] = (reasonCounts[reason] || 0) + 1;
                }
            });

            // Create Flagged Reason Chart (Section)
            const reasonCanvas = document.getElementById('flaggedReasonChartSection');
            if (reasonCanvas) {
                const ctx = reasonCanvas.getContext('2d');
                
                if (flaggedReasonChartSectionInstance) {
                    flaggedReasonChartSectionInstance.destroy();
                }
                
                const labels = Object.keys(reasonCounts);
                const data = Object.values(reasonCounts);
                const colors = [
                    '#e74c3c', '#3498db', '#f39c12', '#9b59b6', '#1abc9c',
                    '#e67e22', '#2ecc71', '#34495e', '#16a085', '#d35400'
                ];
                
                flaggedReasonChartSectionInstance = new Chart(ctx, {
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
    
// Content Management JavaScript
let currentIntentId = null;
let currentQuestionId = null;
let deleteType = ''; // 'intent' or 'question'
let deleteId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize content management tabs
    initContentManagementTabs();
    
    // Load intents and guided questions
    loadIntents();
    loadGuidedQuestions();
    
    // Set up search functionality
    document.getElementById('searchIntents').addEventListener('input', filterIntents);
    document.getElementById('searchGuidedQuestions').addEventListener('input', filterGuidedQuestions);
    
    // Set up filters
    document.getElementById('intentFilter').addEventListener('change', filterIntents);
    document.getElementById('questionFilter').addEventListener('change', filterGuidedQuestions);
    
    // Set up form submissions
    document.getElementById('intentForm').addEventListener('submit', saveIntent);
    document.getElementById('guidedQuestionForm').addEventListener('submit', saveGuidedQuestion);
    
    // Show/hide custom response field based on response type
    document.getElementById('response_type').addEventListener('change', function() {
        const container = document.getElementById('customResponseContainer');
        if (this.value === 'text') {
            container.style.display = 'none';
        } else {
            container.style.display = 'block';
        }
    });
});

function initContentManagementTabs() {
    const tabButtons = document.querySelectorAll('#content .dashboard-tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tab buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Hide all tab contents
            document.querySelectorAll('#content .dashboard-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show target tab content
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

// Intent Management Functions
function openCreateIntentModal() {
    document.getElementById('intentModalTitle').textContent = 'Create New Intent';
    document.getElementById('intentForm').reset();
    document.getElementById('intent_id').value = '';
    currentIntentId = null;
    document.getElementById('intentModal').classList.add('active');
}

function openEditIntentModal(intentId, intentData) {
    document.getElementById('intentModalTitle').textContent = 'Edit Intent';
    document.getElementById('intent_id').value = intentId;
    currentIntentId = intentId;
    
    // Populate form fields
    document.getElementById('intent_name').value = intentData.intent_name || '';
    document.getElementById('display_name').value = intentData.display_name || '';
    document.getElementById('description').value = intentData.description || '';
    document.getElementById('training_phrases').value = intentData.training_phrases ? 
        intentData.training_phrases.join('\n') : '';
    document.getElementById('responses').value = intentData.responses ? 
        intentData.responses.join('\n') : '';
    document.getElementById('parameters').value = intentData.parameters || '';
    document.getElementById('priority').value = intentData.priority || 'normal';
    document.getElementById('status').value = intentData.status || 'active';
    
    document.getElementById('intentModal').classList.add('active');
}

function closeIntentModal() {
    document.getElementById('intentModal').classList.remove('active');
}

function openDeleteIntentModal(intentId, intentName) {
    deleteType = 'intent';
    deleteId = intentId;
    document.getElementById('deleteMessage').textContent = 
        `Are you sure you want to delete the intent "${intentName}"? This action cannot be undone.`;
    document.getElementById('confirmDeleteModal').classList.add('active');
}

// Guided Questions Functions
function openCreateGuidedQuestionModal() {
    document.getElementById('guidedQuestionModalTitle').textContent = 'Add Guided Question';
    document.getElementById('guidedQuestionForm').reset();
    document.getElementById('guided_question_id').value = '';
    currentQuestionId = null;
    
    // Load intents for the dropdown
    loadIntentsForDropdown();
    
    document.getElementById('guidedQuestionModal').classList.add('active');
}

function openEditGuidedQuestionModal(questionId, questionData) {
    document.getElementById('guidedQuestionModalTitle').textContent = 'Edit Guided Question';
    document.getElementById('guided_question_id').value = questionId;
    currentQuestionId = questionId;
    
    // Load intents for the dropdown first
    loadIntentsForDropdown(function() {
        // Populate form fields after intents are loaded
        document.getElementById('question_text').value = questionData.question_text || '';
        document.getElementById('linked_intent').value = questionData.linked_intent || '';
        document.getElementById('display_order').value = questionData.display_order || 1;
        document.getElementById('response_type').value = questionData.response_type || 'text';
        document.getElementById('category').value = questionData.category || '';
        document.getElementById('status_gq').value = questionData.status || 'active';
        
        if (questionData.custom_response) {
            document.getElementById('custom_response').value = questionData.custom_response;
        }
        
        // Show/hide custom response container
        const container = document.getElementById('customResponseContainer');
        if (questionData.response_type === 'text') {
            container.style.display = 'none';
        } else {
            container.style.display = 'block';
        }
        
        document.getElementById('guidedQuestionModal').classList.add('active');
    });
}

function closeGuidedQuestionModal() {
    document.getElementById('guidedQuestionModal').classList.remove('active');
}

function openDeleteGuidedQuestionModal(questionId, questionText) {
    deleteType = 'question';
    deleteId = questionId;
    document.getElementById('deleteMessage').textContent = 
        `Are you sure you want to delete the question "${questionText}"?`;
    document.getElementById('confirmDeleteModal').classList.add('active');
}

function closeConfirmDeleteModal() {
    document.getElementById('confirmDeleteModal').classList.remove('active');
    deleteType = '';
    deleteId = null;
}

function confirmDelete() {
    if (deleteType === 'intent') {
        deleteIntent(deleteId);
    } else if (deleteType === 'question') {
        deleteGuidedQuestion(deleteId);
    }
    closeConfirmDeleteModal();
}

// API Functions
async function loadIntents() {
    console.log('📋 Loading intents from server...');
    
    try {
        const response = await fetch('/admin/dialogflow/intents/active');
        console.log('📥 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📊 Received data:', result);
        
        let intents = [];
        
        // Handle different response formats
        if (result.data && Array.isArray(result.data)) {
            // Format 1: { data: [...] }
            intents = result.data;
        } else if (result.intents && Array.isArray(result.intents)) {
            // Format 2: { intents: [...] }
            intents = result.intents;
        } else if (Array.isArray(result)) {
            // Format 3: direct array
            intents = result;
        } else if (result.data && result.data.intents && Array.isArray(result.data.intents)) {
            // Format 4: { data: { intents: [...] } }
            intents = result.data.intents;
        } else {
            console.warn('⚠️ Unexpected response format:', result);
        }
        
        console.log('✅ Intents received:', intents);
        
        // Update the table
        await updateIntentsTable(intents);
        
        return intents;
        
    } catch (error) {
        console.error('❌ Error loading intents:', error);
        showNotification('Failed to load intents: ' + error.message, 'error');
        throw error;
    }
}

async function loadIntentsForDropdown(callback) {
    try {
        const response = await fetch('/admin/dialogflow/intents/active', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Failed to load intents');
        
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('linked_intent');
            select.innerHTML = '<option value="">Select Intent</option>';
            
            data.intents.forEach(intent => {
                const option = document.createElement('option');
                option.value = intent.intent_name;
                option.textContent = intent.display_name;
                select.appendChild(option);
            });
            
            if (callback) callback();
        }
    } catch (error) {
        console.error('Error loading intents for dropdown:', error);
    }
}

async function loadGuidedQuestions() {
    try {
        const response = await fetch('/admin/guided-questions', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) throw new Error('Failed to load guided questions');
        
        const data = await response.json();
        
        if (data.success) {
            updateGuidedQuestionsTable(data.questions);
        }
    } catch (error) {
        console.error('Error loading guided questions:', error);
        document.getElementById('guidedQuestionsTableBody').innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                    Error loading guided questions. Please try again.
                </td>
            </tr>
        `;
    }
}

async function saveIntent(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const intentId = formData.get('intent_id');
    const url = intentId ? `/admin/dialogflow/intents/${intentId}` : '/admin/dialogflow/intents';
    const method = intentId ? 'PUT' : 'POST';
    
    // Convert training phrases and responses to arrays
    const trainingPhrases = formData.get('training_phrases').split('\n').filter(phrase => phrase.trim());
    const responses = formData.get('responses').split('\n').filter(response => response.trim());
    
    const data = {
        intent_name: formData.get('intent_name'),
        display_name: formData.get('display_name'),
        description: formData.get('description'),
        training_phrases: trainingPhrases,
        responses: responses,
        parameters: formData.get('parameters'),
        priority: formData.get('priority'),
        status: formData.get('status')
    };
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Intent saved successfully!', 'success');
            closeIntentModal();
            loadIntents();
            loadGuidedQuestions(); // Reload questions as they might be linked to intents
        } else {
            showNotification(result.message || 'Failed to save intent', 'error');
        }
    } catch (error) {
        console.error('Error saving intent:', error);
        showNotification('Error saving intent', 'error');
    }
}

async function saveGuidedQuestion(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const questionId = formData.get('guided_question_id');
    const url = questionId ? `/admin/guided-questions/${questionId}` : '/admin/guided-questions';
    const method = questionId ? 'PUT' : 'POST';
    
    const data = {
        question_text: formData.get('question_text'),
        linked_intent: formData.get('linked_intent'),
        display_order: parseInt(formData.get('display_order')),
        response_type: formData.get('response_type'),
        custom_response: formData.get('custom_response'),
        category: formData.get('category'),
        status: formData.get('status_gq')
    };
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Guided question saved successfully!', 'success');
            closeGuidedQuestionModal();
            loadGuidedQuestions();
        } else {
            showNotification(result.message || 'Failed to save question', 'error');
        }
    } catch (error) {
        console.error('Error saving guided question:', error);
        showNotification('Error saving guided question', 'error');
    }
}

async function deleteIntent(intentId) {
    try {
        const response = await fetch(`/admin/dialogflow/intents/${intentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Intent deleted successfully!', 'success');
            loadIntents();
        } else {
            showNotification(result.message || 'Failed to delete intent', 'error');
        }
    } catch (error) {
        console.error('Error deleting intent:', error);
        showNotification('Error deleting intent', 'error');
    }
}

async function deleteGuidedQuestion(questionId) {
    try {
        const response = await fetch(`/admin/guided-questions/${questionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Guided question deleted successfully!', 'success');
            loadGuidedQuestions();
        } else {
            showNotification(result.message || 'Failed to delete question', 'error');
        }
    } catch (error) {
        console.error('Error deleting guided question:', error);
        showNotification('Error deleting guided question', 'error');
    }
}

async function syncWithDialogflow() {
    let originalText = 'Sync with Dialogflow';
    let syncBtn = null;
    
    try {
        syncBtn = document.querySelector('button[onclick="syncWithDialogflow()"]');
        originalText = syncBtn ? syncBtn.innerHTML : 'Sync with Dialogflow';
        
        if (syncBtn) {
            syncBtn.disabled = true;
            syncBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
        }
        
        console.log('🔄 Starting Dialogflow sync...');
        showNotification('Syncing with Dialogflow...', 'info');
        
        const response = await fetch('/admin/dialogflow/sync', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('📥 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📊 Sync result:', result);
        
        if (result.success) {
            // Show success message
            const message = result.data && result.data.is_mock_data ?
                `Using mock data: Found ${result.data.intents_synced} intents. Check Dialogflow credentials.` :
                `Success! Found ${result.data.intents_synced} intents.`;
            
            showNotification(message, result.data && result.data.is_mock_data ? 'warning' : 'success');
            
            // Check if we have intents to display
            let intents = [];
            if (result.data && result.data.intents && Array.isArray(result.data.intents)) {
                intents = result.data.intents;
                console.log(`✅ Received ${intents.length} intents from sync`);
            } else {
                console.warn('⚠️ No intents found in sync response:', result);
                // Try to load intents from the separate endpoint
                intents = await loadIntents();
            }
            
            // Update the table with intents
            if (intents.length > 0) {
                await updateIntentsTable(intents);
            } else {
                showNotification('No intents found in Dialogflow', 'warning');
            }
            
        } else {
            console.error('❌ Sync failed:', result.message);
            showNotification(result.message || 'Failed to sync with Dialogflow', 'error');
        }
        
    } catch (error) {
        console.error('💥 Sync error:', error);
        showNotification('Error syncing with Dialogflow: ' + error.message, 'error');
        
        // Try to load existing intents anyway
        try {
            await loadIntents();
        } catch (loadError) {
            console.error('Failed to load intents:', loadError);
        }
    } finally {
        // Restore button state
        if (syncBtn) {
            syncBtn.disabled = false;
            syncBtn.innerHTML = originalText;
        }
    }
}
async function updateIntentsTable(intents) {
    try {
        console.log('📊 Updating table with', intents.length, 'intents');
        
        // Try multiple possible table body IDs
        let tableBody = document.getElementById('intentsTableBody') || 
                       document.getElementById('intents-table-body');
        
        if (!tableBody) {
            console.error('❌ Table body not found! Looking for:');
            console.error('- intentsTableBody:', !!document.getElementById('intentsTableBody'));
            console.error('- intents-table-body:', !!document.getElementById('intents-table-body'));
            
            // Try to find any table body in the content management section
            const contentSection = document.getElementById('intent-management');
            if (contentSection) {
                tableBody = contentSection.querySelector('tbody');
                console.log('Found table body in section:', !!tableBody);
            }
        }
        
        if (!tableBody) {
            showNotification('Could not find intents table on page', 'warning');
            return;
        }
        
        // Clear existing rows
        tableBody.innerHTML = '';
        
        if (!intents || intents.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #666;">
                        <i class="fas fa-inbox mr-2"></i>
                        No intents found. Sync with Dialogflow first.
                    </td>
                </tr>
            `;
            return;
        }
        
        // Add intents to table
        intents.forEach((intent, index) => {
            const row = document.createElement('tr');
            row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
            row.innerHTML = `
                <td style="padding: 12px; white-space: nowrap;">${index + 1}</td>
                <td style="padding: 12px; white-space: nowrap;">
                    <div style="font-weight: 600;">${intent.display_name || 'No name'}</div>
                    <div style="font-size: 12px; color: #666; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                        ${intent.id || 'No ID'}
                    </div>
                </td>
                <td style="padding: 12px;">
                    <div>${intent.training_phrases_count || 0}</div>
                    <div style="font-size: 12px; color: #666; margin-top: 4px; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                        ${(intent.training_phrases || []).slice(0, 2).map(p => `"${p}"`).join(', ')}
                        ${(intent.training_phrases_count || 0) > 2 ? '...' : ''}
                    </div>
                </td>
                <td style="padding: 12px;">
                    <div>${intent.responses_count || 0}</div>
                    <div style="font-size: 12px; color: #666; margin-top: 4px; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                        ${(intent.responses || []).slice(0, 1).map(r => {
                            const text = String(r || '');
                            return `"${text.substring(0, 50)}${text.length > 50 ? '...' : ''}"`;
                        }).join(', ')}
                        ${(intent.responses_count || 0) > 1 ? '...' : ''}
                    </div>
                </td>
                <td style="padding: 12px; white-space: nowrap;">
                    <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;
                        ${intent.is_fallback ? 'background: #fee; color: #c00;' : 'background: #efe; color: #090;'}">
                        ${intent.is_fallback ? 'Fallback' : 'Regular'}
                    </span>
                </td>
                <td style="padding: 12px; white-space: nowrap;">
                    <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;
                        ${(intent.status || 'active') === 'active' ? 'background: #efe; color: #090;' : 'background: #eee; color: #666;'}">
                        ${intent.status || 'active'}
                    </span>
                </td>
                <td style="padding: 12px; white-space: nowrap; font-size: 14px; color: #666;">
                    ${intent.priority || 'normal'}
                </td>
                <td style="padding: 12px; white-space: nowrap; text-align: right;">
                    <button onclick="editIntent('${intent.id}')" style="color: #4f46e5; margin-right: 12px;">
                        Edit
                    </button>
                    <button onclick="deleteIntent('${intent.id}')" style="color: #dc2626;">
                        Delete
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
        
        // Update the counter
        const counterElement = document.getElementById('intents-count');
        if (counterElement) {
            counterElement.textContent = intents.length;
        }
        
        console.log('✅ Table updated successfully');
        
    } catch (error) {
        console.error('❌ Error updating table:', error);
        throw error;
    }
}

function updateGuidedQuestionsTable(questions) {
    const tbody = document.getElementById('guidedQuestionsTableBody');
    
    if (!questions || questions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                    No guided questions found. Click "Add Guided Question" to add your first question.
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    questions.forEach(question => {
        html += `
            <tr>
                <td>
                    <strong>${question.question_text}</strong>
                    ${question.category ? `<br><small style="color: #666;">Category: ${question.category}</small>` : ''}
                </td>
                <td>${question.linked_intent || 'N/A'}</td>
                <td>${question.display_order}</td>
                <td>
                    <span class="status ${question.response_type === 'text' ? 'normal' : 'flagged'}">
                        ${question.response_type}
                    </span>
                </td>
                <td>
                    <span class="status ${question.status === 'active' ? 'open' : 'resolved'}">
                        ${question.status === 'active' ? 'Active' : 'Archived'}
                    </span>
                </td>
                <td>${formatDate(question.created_at)}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-action btn-edit" onclick="openEditGuidedQuestionModal('${question.id}', ${JSON.stringify(question).replace(/'/g, "\\'")})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-action btn-delete" onclick="openDeleteGuidedQuestionModal('${question.id}', '${question.question_text.substring(0, 50).replace(/'/g, "\\'")}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function updateStats(stats) {
    if (!stats) return;
    
    if (stats.total_intents !== undefined) {
        document.getElementById('totalIntents').textContent = stats.total_intents;
    }
    
    if (stats.total_questions !== undefined) {
        document.getElementById('totalGuidedQuestions').textContent = stats.total_questions;
    }
    
    if (stats.total_training_phrases !== undefined) {
        document.getElementById('totalTrainingPhrases').textContent = stats.total_training_phrases;
    }
    
    if (stats.last_updated) {
        document.getElementById('lastUpdatedTime').textContent = formatTimeAgo(stats.last_updated);
    }
}

// Filter Functions
function filterIntents() {
    const searchTerm = document.getElementById('searchIntents').value.toLowerCase();
    const filterValue = document.getElementById('intentFilter').value;
    const rows = document.querySelectorAll('#intentsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusCell = row.querySelector('.status');
        const status = statusCell ? statusCell.textContent.toLowerCase() : '';
        
        let showRow = true;
        
        // Check search term
        if (searchTerm && !text.includes(searchTerm)) {
            showRow = false;
        }
        
        // Check filter
        if (filterValue !== 'all') {
            if (filterValue === 'active' && status !== 'active') {
                showRow = false;
            } else if (filterValue === 'inactive' && status !== 'inactive') {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function filterGuidedQuestions() {
    const searchTerm = document.getElementById('searchGuidedQuestions').value.toLowerCase();
    const filterValue = document.getElementById('questionFilter').value;
    const rows = document.querySelectorAll('#guidedQuestionsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusCell = row.querySelector('.status');
        const status = statusCell ? statusCell.textContent.toLowerCase() : '';
        
        let showRow = true;
        
        // Check search term
        if (searchTerm && !text.includes(searchTerm)) {
            showRow = false;
        }
        
        // Check filter
        if (filterValue !== 'all') {
            if (filterValue === 'active' && status !== 'active') {
                showRow = false;
            } else if (filterValue === 'archived' && status !== 'archived') {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// =============================================
// STAR RATING FILTER FUNCTIONS
// =============================================
let currentStarFilter = 'all'; // For feedback tab (backward compat)
let currentStarFilterSection = 'all'; // For feedback section (backward compat)

function filterFeedbackByStars(stars, btnElement) {
    currentStarFilter = stars;
    // Update unified filter state
    filterState.starRating.feedback = stars;
    
    // Update button styles
    document.querySelectorAll('.star-filter-btn').forEach(btn => {
        if (btn === btnElement) {
            btn.style.background = btn.dataset.stars === 'all' ? 'var(--primary)' : btn.style.color;
            btn.style.color = 'white';
            btn.classList.add('active');
        } else {
            const color = btn.dataset.stars === 'all' ? 'var(--primary)' : 
                         btn.dataset.stars === '5' ? '#27ae60' :
                         btn.dataset.stars === '4' ? '#2ecc71' :
                         btn.dataset.stars === '3' ? '#f39c12' :
                         btn.dataset.stars === '2' ? '#e67e22' : '#e74c3c';
            btn.style.background = 'white';
            btn.style.color = color;
            btn.classList.remove('active');
        }
    });
    
    // Apply unified filter (chains with date filter)
    const result = applyAllFeedbackFilters('feedbackDashTable', 'feedback');
    if (result) {
        const avgRating = result.visibleCount > 0 ? (result.totalRating / result.visibleCount).toFixed(2) : 'N/A';
        document.getElementById('avgFeedbackValue').textContent = avgRating;
        document.getElementById('avgFeedbackCount').textContent = `Based on ${result.visibleCount} feedback${result.visibleCount === 1 ? '' : 's'}`;
        updatePagificationDisplay('feedback', result.visibleCount, result.isFiltered);
    }
    
    // Update chart
    if (typeof initFeedbackCharts === 'function') {
        initFeedbackCharts();
    }
}

function filterFeedbackSectionByStars(stars, btnElement) {
    currentStarFilterSection = stars;
    // Update unified filter state
    filterState.starRating.feedbackSection = stars;
    
    // Update button styles
    document.querySelectorAll('.star-filter-section-btn').forEach(btn => {
        if (btn === btnElement) {
            btn.style.background = btn.dataset.stars === 'all' ? 'var(--primary)' : btn.style.color;
            btn.style.color = 'white';
            btn.classList.add('active');
        } else {
            const color = btn.dataset.stars === 'all' ? 'var(--primary)' : 
                         btn.dataset.stars === '5' ? '#27ae60' :
                         btn.dataset.stars === '4' ? '#2ecc71' :
                         btn.dataset.stars === '3' ? '#f39c12' :
                         btn.dataset.stars === '2' ? '#e67e22' : '#e74c3c';
            btn.style.background = 'white';
            btn.style.color = color;
            btn.classList.remove('active');
        }
    });
    
    // Apply unified filter (chains with date filter)
    const result = applyAllFeedbackFilters('feedbackSectionTable', 'feedbackSection');
    if (result) {
        const avgRating = result.visibleCount > 0 ? (result.totalRating / result.visibleCount).toFixed(2) : 'N/A';
        document.getElementById('avgFeedbackValueSection').textContent = avgRating;
        document.getElementById('avgFeedbackCountSection').textContent = `Based on ${result.visibleCount} feedback${result.visibleCount === 1 ? '' : 's'}`;
        updatePagificationDisplay('feedback-section', result.visibleCount, result.isFiltered);
    }
    
    // Update chart
    if (typeof initFeedbackSectionCharts === 'function') {
        initFeedbackSectionCharts();
    }
    
    // Also update flagged reason KPI for feedback section
    if (typeof updateFlaggedReasonKPISection === 'function') {
        updateFlaggedReasonKPISection();
    }
}

// Utility Functions
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

function formatTimeAgo(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} mins ago`;
    if (diffHours < 24) return `${diffHours} hours ago`;
    if (diffDays < 7) return `${diffDays} days ago`;
    
    return formatDate(dateString);
}

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

// =============================================
// AJAX FORM HANDLERS FOR SEAMLESS UX
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    // CREATE ACCOUNT FORM AJAX HANDLER
    const createForm = document.getElementById('createAccountForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = createForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            
            try {
                const formData = new FormData(createForm);
                const response = await fetch(createForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Account created successfully!', 'success');
                    closeCreateModal();
                    createForm.reset();
                    // Reload page to refresh the table
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?active_tab=account-management';
                    }, 1000);
                } else {
                    showNotification(result.message || 'Failed to create account', 'error');
                }
            } catch (error) {
                console.error('Error creating account:', error);
                showNotification('An error occurred while creating the account.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // EDIT ACCOUNT FORM AJAX HANDLER
    const editForm = document.getElementById('editAccountForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            try {
                const formData = new FormData(editForm);
                const response = await fetch(editForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Account updated successfully!', 'success');
                    closeEditModal();
                    // Reload page to refresh the table
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?active_tab=account-management';
                    }, 1000);
                } else {
                    showNotification(result.message || 'Failed to update account', 'error');
                }
            } catch (error) {
                console.error('Error updating account:', error);
                showNotification('An error occurred while updating the account.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // RESET PASSWORD FORM AJAX HANDLER
    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = resetForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
            
            try {
                const formData = new FormData(resetForm);
                const response = await fetch(resetForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Password reset successfully!', 'success');
                    closeResetModal();
                    resetForm.reset();
                } else {
                    showNotification(result.message || 'Failed to reset password', 'error');
                }
            } catch (error) {
                console.error('Error resetting password:', error);
                showNotification('An error occurred while resetting the password.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // IMPORT ACCOUNTS FORM AJAX HANDLER
    const importForm = document.getElementById('importAccountForm');
    if (importForm) {
        importForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = importForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
            
            try {
                const formData = new FormData(importForm);
                const response = await fetch(importForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Accounts imported successfully!', 'success');
                    closeImportModal();
                    importForm.reset();
                    // Reload page to refresh the table
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?active_tab=account-management';
                    }, 1000);
                } else {
                    showNotification(result.message || 'Failed to import accounts', 'error');
                }
            } catch (error) {
                console.error('Error importing accounts:', error);
                showNotification('An error occurred while importing accounts.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
});

// ARCHIVE ACCOUNT WITH AJAX (updated version)
function archiveAccountAjax(employeeNum) {
    if (!confirm(`Are you sure you want to archive account ${employeeNum}? The account will be archived and deactivated but data will be preserved.`)) {
        return;
    }
    
    fetch(`/admin/accounts/${employeeNum}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification(result.message || 'Account archived successfully!', 'success');
            // Reload page to refresh the table
            setTimeout(() => {
                window.location.href = window.location.pathname + '?active_tab=account-management';
            }, 1000);
        } else {
            showNotification(result.message || 'Failed to archive account', 'error');
        }
    })
    .catch(error => {
        console.error('Error archiving account:', error);
        showNotification('An error occurred while archiving the account.', 'error');
    });
}
</script>
    <script src="/assets/js/feedback_kpi.js"></script>
</body>
</html>
