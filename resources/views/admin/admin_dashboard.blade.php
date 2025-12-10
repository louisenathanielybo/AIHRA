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
        content: '⇅';
        position: absolute;
        right: 8px;
        opacity: 0.3;
        font-size: 0.8rem;
    }

    .sortable.asc::after {
        content: '▲';
        opacity: 1;
    }

    .sortable.desc::after {
        content: '▼';
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
    <div class="account-info">
        <div class="account-avatar">
            {{ substr(Auth::user()->firstName, 0, 1) }}{{ substr(Auth::user()->lastName, 0, 1) }}
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
            {{ substr(Auth::user()->firstName, 0, 1) }}{{ substr(Auth::user()->lastName, 0, 1) }}
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
                <div class="data-table">
                    <h3>Recent Chatbot Interactions ({{ $recentInteractions->total() }} total)</h3>
                    <table id="interactionsTable">
                        <thead>
                            <tr>
                                <th>Query</th>
                                <th class="sortable" onclick="sortTable('interactionsTable', 1, 'number')">Response Time</th>
                                <th class="sortable" onclick="sortTable('interactionsTable', 2, 'text')">Status</th>
                                <th class="sortable" onclick="sortTable('interactionsTable', 3, 'date')">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInteractions as $interaction)
                            <tr data-date="{{ \Carbon\Carbon::parse($interaction->questionTime)->format('Y-m-d') }}" data-escalated="{{ $interaction->isEscalated ? '1' : '0' }}">
                                <td>{{ \Illuminate\Support\Str::limit($interaction->question, 50) }}</td>
                                <td>
                                    @if(isset($interaction->response_time_ms) && $interaction->response_time_ms !== null)
                                        {{ number_format($interaction->response_time_ms, 2) }}ms
                                    @else
                                        0.00ms
                                    @endif
                                </td>
                                <td>
                                    <span class="status {{ $interaction->isEscalated ? 'flagged' : 'normal' }}">
                                        {{ $interaction->isEscalated ? 'Escalated' : 'Normal' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($interaction->questionTime)->format('d/m/y') }}</td>
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

                <div class="data-table">
                    <h3>User Feedback</h3>
                    <table id="feedbackDashTable">
                        <thead>
                            <tr>
                                <th class="sortable" onclick="sortTable('feedbackDashTable', 0, 'number')">Feedback ID</th>
                                <th class="sortable" onclick="sortTable('feedbackDashTable', 1, 'number')">Rating</th>
                                <th>Subject</th>
                                <th class="sortable" onclick="sortTable('feedbackDashTable', 3, 'date')">Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbackData as $index => $feedback)
                            <tr>
                                <td><strong>#{{ str_pad($feedback->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
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
                                <td><strong>#{{ str_pad($flagged->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
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
            <div class="data-table">
                <h3>User Feedback</h3>
                <table id="feedbackSectionTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('feedbackSectionTable', 0, 'number')">Feedback ID</th>
                            <th class="sortable" onclick="sortTable('feedbackSectionTable', 1, 'number')">Rating</th>
                            <th>Subject</th>
                            <th class="sortable" onclick="sortTable('feedbackSectionTable', 3, 'date')">Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbackData as $feedback)
                        <tr>
                            <td><strong>#{{ str_pad($feedback->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
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
                            <td><strong>#{{ str_pad($flagged->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
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
                    <div class="trend {{ ($totalTicketsChange ?? 0) >= 0 ? 'up' : 'down' }}">
                        {{ ($totalTicketsChange ?? 0) >= 0 ? '+' : '' }}{{ $totalTicketsChange ?? 0 }} from last week
                    </div>
                </div>
                <div class="card stat-card">
                    <h3>Unresolved Tickets</h3>
                    <div class="value" id="unresolvedTickets">{{ $unresolvedTickets ?? 0 }}</div>
                    <div class="trend {{ ($unresolvedTicketsChange ?? 0) >= 0 ? 'up' : 'down' }}">
                        {{ ($unresolvedTicketsChange ?? 0) >= 0 ? '+' : '' }}{{ $unresolvedTicketsChange ?? 0 }} from last week
                    </div>
                </div>
                <div class="card stat-card">
                    <h3>Resolved Tickets</h3>
                    <div class="value" id="resolvedTickets">{{ $resolvedTickets ?? 0 }}</div>
                    <div class="trend {{ ($resolvedTicketsChange ?? 0) >= 0 ? 'up' : 'down' }}">
                        {{ ($resolvedTicketsChange ?? 0) >= 0 ? '+' : '' }}{{ $resolvedTicketsChange ?? 0 }} from last week
                    </div>
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
                        <i class="fas fa-upload"></i> Import CSV
                    </button>
                    <button class="btn-secondary" onclick="exportAccounts()">
                        <i class="fas fa-download"></i> Export CSV
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
                            <th class="sortable" onclick="sortTable('accountsTable', 4, 'text')">Role</th>
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
                                <button class="btn-action btn-edit" onclick="editAccountModal('{{ $user->employeeNum }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-action btn-reset" onclick="resetPasswordModal('{{ $user->employeeNum }}')">
                                    <i class="fas fa-key"></i> Reset
                                </button>
                                @if($user->employeeNum != Auth::user()->employeeNum)
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
                        <li><strong>Required columns:</strong> Employee Number, Email, First Name, Last Name, Role, Gender, Date of Birth, Status</li>
                        <li><strong>Date format:</strong> YYYY-MM-DD (e.g., 1990-05-15)</li>
                        <li><strong>Role options:</strong> Employee, Admin, HR</li>
                        <li><strong>Gender options:</strong> Male, Female</li>
                        <li><strong>Status options:</strong> Active, Deactivated</li>
                        <li><strong>Default password:</strong> All imported accounts will have password "Welcome@123"</li>
                        <li><strong>Tip:</strong> Export existing accounts to get a template</li>
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
                            <i class="fas fa-upload"></i> Import Accounts
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

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Store active tab from server
        const activeTab = '{{ $active_tab }}';

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
        });

        // Restore the active tab on page load
        function restoreActiveTab() {
            if (activeTab && activeTab !== 'dashboard') {
                const targetLink = document.querySelector(`[data-section="${activeTab}"]`);
                if (targetLink) {
                    updateActiveStates(targetLink, activeTab);
                    updatePageTitle(activeTab);
                }
            }
        }

        // Initialize sidebar navigation
        function initSidebarNavigation() {
            const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
            
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the target section
                    const targetSection = this.getAttribute('data-section');
                    
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
            // Remove active class from all sidebar links
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
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
            
            switch(section) {
                case 'dashboard':
                    pageTitle.textContent = 'Dashboard Overview';
                    if (filterContainer) filterContainer.style.display = 'flex';
                    break;
                case 'performance':
                    pageTitle.textContent = 'Chatbot Performance';
                    if (filterContainer) filterContainer.style.display = 'none';
                    break;
                case 'feedback':
                    pageTitle.textContent = 'User Feedback';
                    if (filterContainer) filterContainer.style.display = 'flex';
                    break;
                case 'content':
                    pageTitle.textContent = 'Content Management';
                    if (filterContainer) filterContainer.style.display = 'none';
                    break;
                case 'tickets':
                    pageTitle.textContent = 'Chatbot Ticket Details';
                    if (filterContainer) filterContainer.style.display = 'flex';
                    break;
                case 'account-management':
                    pageTitle.textContent = 'Account Management';
                    if (filterContainer) filterContainer.style.display = 'none';
                    break;
            }
        }

        // Initialize dashboard tabs
        function initDashboardTabs() {
            const tabButtons = document.querySelectorAll('.dashboard-tab-btn');
            
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
                    document.querySelectorAll('.dashboard-tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show target tab content
                    document.getElementById(targetTab).classList.add('active');
                });
            });
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
            
            // Update statistics
            updateStatistics();
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
            const feedbackRows = document.querySelectorAll('#feedback table tbody tr, #feedback-section table tbody tr');
            
            feedbackRows.forEach(row => {
                const dateCell = row.querySelector('td:last-child'); // Last column is date
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
            const interactionRows = document.querySelectorAll('#performance table tbody tr');
            
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
            // Count visible tickets
            const visibleTicketRows = Array.from(document.querySelectorAll('#ticketsTable tbody tr'))
                .filter(row => row.style.display !== 'none' && !row.querySelector('td[colspan]'));
            
            const totalVisible = visibleTicketRows.length;
            const unresolvedVisible = visibleTicketRows.filter(row => {
                const statusCell = row.querySelector('.status');
                return statusCell && statusCell.textContent.toLowerCase().includes('open');
            }).length;
            const resolvedVisible = visibleTicketRows.filter(row => {
                const statusCell = row.querySelector('.status');
                return statusCell && statusCell.textContent.toLowerCase().includes('resolved');
            }).length;
            
            // Update ticket stats if on tickets page
            const totalTicketsEl = document.getElementById('totalTickets');
            const unresolvedTicketsEl = document.getElementById('unresolvedTickets');
            const resolvedTicketsEl = document.getElementById('resolvedTickets');
            
            if (totalTicketsEl) totalTicketsEl.textContent = totalVisible;
            if (unresolvedTicketsEl) unresolvedTicketsEl.textContent = unresolvedVisible;
            if (resolvedTicketsEl) resolvedTicketsEl.textContent = resolvedVisible;
            
            // Update dashboard KPIs and chart
            updateDashboardKPIs();
        }
        
        // Update dashboard KPIs based on visible filtered data
        function updateDashboardKPIs() {
            const { start, end } = dateRangeData;
            
            // Use server-side totals instead of counting paginated visible rows
            const totalInteractions = serverTotalInteractions;
            const escalatedQueries = serverEscalatedCount;
            const pendingQueries = serverPendingQueries;
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

        // Initialize date range filter on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyDateRangeFilter(); // Apply default filter (daily)
        });

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
        }

        // =============================================
        // CHATBOT TICKETS FUNCTIONALITY
        // =============================================
        let currentTicketId = null;

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
                
                // Refresh the page to show updated status
                setTimeout(() => {
                    window.location.reload();
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
                window.location.href = url.toString();
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
            if (confirm(`Are you sure you want to delete account ${employeeNum}? This action cannot be undone.`)) {
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
                    // Parse dates
                    aValue = new Date(aValue);
                    bValue = new Date(bValue);
                    return direction === 'asc' ? aValue - bValue : bValue - aValue;
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
            // Get feedback rating data from table
            const feedbackRows = document.querySelectorAll('#feedbackDashTable tbody tr:not([colspan])');
            const ratingCounts = { '1': 0, '2': 0, '3': 0, '4': 0, '5': 0 };
            
            feedbackRows.forEach(row => {
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

            // Get flagged responses reason data from table
            const flaggedRows = document.querySelectorAll('#flaggedDashTable tbody tr:not([colspan])');
            const reasonCounts = {};
            
            flaggedRows.forEach(row => {
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
            if (!activeTab || activeTab === 'dashboard') return;

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