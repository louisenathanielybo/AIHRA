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
        --sidebar-width: 250px;
        --card-bg: #ffffff;
        --hover-light: #e8f5e8;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    /* Dialogflow specific styles */
.dialogflow-header {
    margin-bottom: 30px;
}

.dialogflow-header h2 {
    color: var(--primary);
    font-size: 1.8rem;
    margin-bottom: 8px;
}

.dialogflow-header p {
    color: var(--gray);
    font-size: 1rem;
}

/* Badge styles */
.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Form sections */
.form-section {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e8f5e8;
}

/* Action buttons cell */
.action-buttons-cell {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn-action {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s;
    background: #f5f5f5;
    color: #666;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-view { background: #e3f2fd; color: #1976d2; }
.btn-edit { background: #fff3e0; color: #ff9800; }
.btn-test { background: #e8f5e8; color: #4caf50; }
.btn-delete { background: #ffebee; color: #f44336; }

/* Test panel animation */
@keyframes slideIn {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(-10px);
        opacity: 0;
    }
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
        background: linear-gradient(180deg, var(--primary) 0%, var(--dark) 100%);
        color: white;
        height: 100vh;
        position: fixed;
        padding: 20px 0;
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(45, 90, 61, 0.1);
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
        border-left: 4px solid var(--secondary);
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
        padding: 20px;
        background: #f8fdf9;
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
    /* Bulk actions */
.bulk-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    padding: 10px;
    background: #f8fdf9;
    border-radius: 8px;
    border: 1px solid #e0efe5;
    align-items: center;
}

.bulk-checkbox {
    margin-right: 10px;
}

.select-all-text {
    margin-right: auto;
    color: var(--primary);
    font-weight: 500;
}

/* Intent row with checkbox */
.intent-row {
    cursor: pointer;
    transition: background-color 0.2s;
}

.intent-row:hover {
    background-color: var(--hover-light);
}

.intent-row.selected {
    background-color: rgba(74, 140, 94, 0.1);
    border-left: 3px solid var(--secondary);
}

.intent-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
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
            <li>
                <a href="#" class="{{ $active_tab == 'content' ? 'active' : '' }}" 
                data-section="content" onclick="showContentManagement()">
                    <i class="fas fa-cogs"></i> Content Management
                </a>
            </li>
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
    <div class="date-range">Range: {{ now()->format('F d, Y') }}</div>
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
                <div class="chart-container">
                    <h3>Resolution Breakdown</h3>
                    <div class="chart-box">
                        <canvas id="resolutionChart"></canvas>
                    </div>
                </div>
                
                <div class="topics-container">
                    <h3>Most Asked Topics</h3>
                    <div class="topic-item">
                        <span class="topic-name">Payroll</span>
                        <span class="topic-count">13 inquiries</span>
                    </div>
                    <div class="topic-item">
                        <span class="topic-name">Leave</span>
                        <span class="topic-count">8 inquiries</span>
                    </div>
                    <div class="topic-item">
                        <span class="topic-name">Promotion</span>
                        <span class="topic-count">5 inquiries</span>
                    </div>
                    <div class="topic-item">
                        <span class="topic-name">Benefits</span>
                        <span class="topic-count">19 inquiries</span>
                    </div>
                </div>
            </div>
            
            <!-- Performance Tab -->
            <div id="performance" class="dashboard-tab-content">
                <div class="data-table">
                    <h3>Recent Chatbot Interactions</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Query</th>
                                <th>Response Time</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInteractions as $interaction)
                            <tr>
                                <td>{{ $interaction->firstName }} {{ $interaction->lastName }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($interaction->question, 50) }}</td>
                                <td>N/A</td>
                                <td>
                                    <span class="status {{ $interaction->isEscalated ? 'flagged' : 'normal' }}">
                                        {{ $interaction->isEscalated ? 'Escalated' : 'Normal' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($interaction->questionTime)->format('d/m/y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                                    No interactions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            </div>

            <!-- Feedback Tab -->
            <div id="feedback" class="dashboard-tab-content">
                <div class="data-table">
                    <h3>User Feedback</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Feedback</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbackData as $feedback)
                            <tr>
                                <td>{{ $feedback->firstName }} {{ $feedback->lastName }}</td>
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
                                <td>{{ $feedback->suggestion ?? 'No feedback text' }}</td>
                                <td>{{ \Carbon\Carbon::parse($feedback->timeStamp)->format('M d, Y H:i') }}</td>
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
                    <h3>Flagged Responses ({{ count($flaggedResponses ?? []) }})</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Flag ID</th>
                                <th>User</th>
                                <th>Query</th>
                                <th>Reason</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flaggedResponses as $flagged)
                            <tr id="flag-row-{{ $flagged->flaggedID }}">
                                <td><strong>#{{ str_pad($flagged->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $flagged->firstName }} {{ $flagged->lastName }}</td>
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
                                <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
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
                <table>
                    <thead>
                        <tr>
                            <th>Feedback ID</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Feedback</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbackData as $feedback)
                        <tr>
                            <td><strong>#{{ str_pad($feedback->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $feedback->firstName }} {{ $feedback->lastName }}</td>
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
                            <td>{{ $feedback->suggestion ?? 'No feedback text' }}</td>
                            <td>{{ \Carbon\Carbon::parse($feedback->timeStamp)->format('M d, Y H:i') }}</td>
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
            </div>

            <div class="data-table" style="margin-top: 30px;">
                <h3>Flagged Responses ({{ count($flaggedResponses ?? []) }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Flag ID</th>
                            <th>User</th>
                            <th>Query</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flaggedResponses as $flagged)
                        <tr id="flag-row-{{ $flagged->flaggedID }}">
                            <td><strong>#{{ str_pad($flagged->displayID ?? 1, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $flagged->firstName }} {{ $flagged->lastName }}</td>
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
                            <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                No flagged responses found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Content Management Section -->
        <!-- Content Management Section -->
<div id="content" class="section-content">
    <!-- Content Management Tabs -->
    <div class="dashboard-tabs">
        <button class="dashboard-tab-btn active" data-tab="topics">Topics</button>
        <button class="dashboard-tab-btn" data-tab="dialogflow">Dialogflow Intents</button>
        <button class="dashboard-tab-btn" data-tab="guided-questions">Guided Questions</button>
        <button class="dashboard-tab-btn" data-tab="knowledge-base">Knowledge Base</button>
    </div>

    <!-- Topics Tab -->
    <div id="topics" class="dashboard-tab-content active">
        <div class="data-table">
            <h3>Topic Management</h3>
            <div class="action-buttons" style="margin-bottom: 20px;">
                <button class="btn-primary" onclick="openCreateTopicModal()">
                    <i class="fas fa-plus"></i> Add New Topic
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Topic Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Topics will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dialogflow Intents Tab -->
    <div id="dialogflow" class="dashboard-tab-content">
        <div class="dialogflow-header" style="margin-bottom: 30px;">
            <h2>Dialogflow Intent Management</h2>
            <p>Manage your chatbot's intents, training phrases, and responses directly from this interface.</p>
        </div>

        <!-- Stats Cards -->
        <div class="dashboard-cards">
            <div class="card stat-card">
                <h3>Total Intents</h3>
                <div class="value" id="totalIntents">0</div>
                <div class="trend">In Dialogflow</div>
            </div>
            <div class="card stat-card">
                <h3>Training Phrases</h3>
                <div class="value" id="totalPhrases">0</div>
                <div class="trend">Total phrases</div>
            </div>
            <div class="card stat-card">
                <h3>Responses</h3>
                <div class="value" id="totalResponses">0</div>
                <div class="trend">Total responses</div>
            </div>
            <div class="card stat-card">
                <h3>Last Synced</h3>
                <div class="value" id="lastUpdated">-</div>
                <div class="trend" id="updateStatus">Loading...</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons" style="margin-bottom: 20px; display: flex; gap: 10px; justify-content: space-between;">
            <div style="display: flex; gap: 10px;">
                <button class="btn-secondary" onclick="syncIntents()" id="syncButton">
                    <i class="fas fa-sync-alt"></i> Sync Intents
                </button>
                <button class="btn-secondary" onclick="exportIntents()">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="btn-secondary" onclick="importIntentsModal()">
                    <i class="fas fa-upload"></i> Import
                </button>
            </div>
            <div>
                <button class="btn-primary" onclick="createIntentModal()">
                    <i class="fas fa-plus"></i> Create New Intent
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="tickets-header" style="margin-bottom: 20px;">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchIntents" placeholder="Search intents by name or content...">
            </div>
            <div class="filters">
                <div class="filter-group">
                    <label for="typeFilter">Filter by</label>
                    <select id="typeFilter">
                        <option value="all">All Intents</option>
                        <option value="webhook">Webhook Enabled</option>
                        <option value="fallback">Fallback Intents</option>
                        <option value="custom">Custom Intents</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sortFilter">Sort by</label>
                    <select id="sortFilter">
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="updated_desc">Recently Updated</option>
                        <option value="created_desc">Recently Created</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Intents Table -->
        <div class="data-table">
            <h3>Dialogflow Intents</h3>
            <div class="table-responsive">
                <table id="intentsTable">
                    <thead>
                        <tr>
                            <th>Intent Name</th>
                            <th>Training Phrases</th>
                            <th>Responses</th>
                            <th>Webhook</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="intentsTableBody">
                        <tr id="loadingRow">
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <div style="display: inline-block;">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary);"></i>
                                    <p style="margin-top: 10px; color: #666;">Loading intents from Dialogflow...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination" id="intentsPagination" style="display: none;">
                <button class="pagination-btn" onclick="prevPage()">
                    ← Previous
                </button>
                <span class="pagination-info" id="pageInfo">Page 1 of 1</span>
                <button class="pagination-btn" onclick="nextPage()">
                    Next →
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions" style="margin-top: 30px;">
            <div class="quick-action-card" onclick="createIntentModal()">
                <i class="fas fa-plus-circle quick-action-icon"></i>
                <div class="quick-action-text">Create Intent</div>
            </div>
            <div class="quick-action-card" onclick="testIntentModal()">
                <i class="fas fa-play-circle quick-action-icon"></i>
                <div class="quick-action-text">Test Intent</div>
            </div>
            <div class="quick-action-card" onclick="exportIntents()">
                <i class="fas fa-file-export quick-action-icon"></i>
                <div class="quick-action-text">Export All</div>
            </div>
            <div class="quick-action-card" onclick="openTrainingGuide()">
                <i class="fas fa-graduation-cap quick-action-icon"></i>
                <div class="quick-action-text">Training Guide</div>
            </div>
        </div>

        <!-- Test Panel (Collapsible) -->
        <div class="data-table" style="margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">Intent Testing</h3>
                <button class="btn-secondary" onclick="toggleTestPanel()" id="toggleTestBtn">
                    <i class="fas fa-chevron-down"></i> Show Test Panel
                </button>
            </div>
            <div id="testPanel" style="display: none;">
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <input type="text" id="testQuery" placeholder="Type a question to test intent matching..." 
                               class="search-box" style="width: 100%; padding: 12px;">
                    </div>
                    <button class="btn-primary" onclick="testIntent()" id="testButton">
                        <i class="fas fa-play"></i> Test
                    </button>
                </div>
                <div id="testResult" style="display: none; background: #f8fdf9; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5; margin-top: 15px;">
                    <h4 style="color: var(--primary); margin-bottom: 15px;">Test Results</h4>
                    <div id="testResultContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guided Questions Tab -->
    <div id="guided-questions" class="dashboard-tab-content">
        <div class="data-table">
            <h3>Guided Questions Management</h3>
            <!-- Add guided questions management here -->
        </div>
    </div>

    <!-- Knowledge Base Tab -->
    <div id="knowledge-base" class="dashboard-tab-content">
        <div class="data-table">
            <h3>Knowledge Base Management</h3>
            <!-- Add knowledge base management here -->
        </div>
    </div>
</div>
        
        <!-- Chatbot Ticket Details Section -->
        <div id="tickets" class="section-content">
            <div class="dashboard-cards">
                <div class="card stat-card">
                    <h3>Total Tickets</h3>
                    <div class="value" id="totalTickets">{{ $totalTickets ?? 0 }}</div>
                    <div class="trend up">+3 from last week</div>
                </div>
                <div class="card stat-card">
                    <h3>Unresolved Tickets</h3>
                    <div class="value" id="unresolvedTickets">{{ $unresolvedTickets ?? 0 }}</div>
                    <div class="trend down">-2 from last week</div>
                </div>
                <div class="card stat-card">
                    <h3>Resolved Tickets</h3>
                    <div class="value" id="resolvedTickets">{{ $resolvedTickets ?? 0 }}</div>
                    <div class="trend up">+5 from last week</div>
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
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
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
                    <button class="btn-secondary" onclick="exportAccounts()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    <button class="btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> Add new account
                    </button>
                </div>
            </div>

            <!-- Accounts Table -->
            <div class="table-responsive">
                <table class="enhanced-table">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
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

            <!-- Pagination -->
            @if($users->hasPages())
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

        // Initialize dashboard functionality
        document.addEventListener('DOMContentLoaded', function() {
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
            
            switch(section) {
                case 'dashboard':
                    pageTitle.textContent = 'Dashboard Overview';
                    break;
                case 'performance':
                    pageTitle.textContent = 'Chatbot Performance';
                    break;
                case 'feedback':
                    pageTitle.textContent = 'User Feedback';
                    break;
                case 'content':
                    pageTitle.textContent = 'Content Management';
                    break;
                case 'tickets':
                    pageTitle.textContent = 'Chatbot Ticket Details';
                    break;
                case 'account-management':
                    pageTitle.textContent = 'Account Management';
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

        // Initialize charts
        function initCharts() {
            // Resolution Breakdown Chart - using real data passed from controller
            const resolutionCtx = document.getElementById('resolutionChart').getContext('2d');
            const resolutionChart = new Chart(resolutionCtx, {
                type: 'bar',
                data: {
                    labels: ['Resolved', 'Pending', 'Escalated'],
                    datasets: [{
                        label: 'Queries',
                        data: [
                            {{ $resolvedQueries }}, 
                            {{ $pendingQueries }}, 
                            {{ $escalatedCount }}
                        ],
                        backgroundColor: [
                            '#2ecc71', // Green for resolved
                            '#f39c12', // Orange for pending  
                            '#3498db'  // Blue for escalated
                        ],
                        borderWidth: 0
                    }]
                },
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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
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
            const priorityFilter = document.getElementById('priorityFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#ticketsTable tbody tr');
            
            rows.forEach(row => {
                const priorityElement = row.querySelector('.priority');
                const statusElement = row.querySelector('.status');
                
                if (!priorityElement || !statusElement) return;
                
                const priority = priorityElement.classList.contains(priorityFilter);
                const status = statusElement.classList.contains(statusFilter);
                
                let showRow = true;
                
                if (priorityFilter !== 'all' && !priority) {
                    showRow = false;
                }
                
                if (statusFilter !== 'all' && !status) {
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
                                    <label>From User:</label>
                                    <div class="value">${ticket.from_user}</div>
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
// DIALOGFLOW INTENT MANAGEMENT
// =============================================

let allIntents = [];
let currentPage = 1;
const intentsPerPage = 10;
let filteredIntents = [];

// Initialize when Dialogflow tab is active
function initDialogflow() {
    loadIntents();
    setupEventListeners();
}

// Setup event listeners for search and filters
function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('searchIntents');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchIntents(e.target.value);
        });
    }

    // Type filter
    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            filterIntents();
        });
    }

    // Sort filter
    const sortFilter = document.getElementById('sortFilter');
    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            sortIntents();
        });
    }
}

// Load intents from API
// Update the loadIntents function
async function loadIntents(pageToken = null) {
    try {
        const loadingRow = document.getElementById('loadingRow');
        if (loadingRow) loadingRow.style.display = '';
        
        const syncBtn = document.getElementById('syncButton');
        if (syncBtn) {
            syncBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            syncBtn.disabled = true;
        }
        
        // Use the basic endpoint first
        const response = await fetch('/admin/dialogflow/intents/api/all', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            allIntents = data.intents;
            filteredIntents = [...allIntents];
            
            // Update UI
            renderIntents();
            updateStats();
            
            if (loadingRow) loadingRow.style.display = 'none';
            
            showNotification(`Loaded ${allIntents.length} intents`, 'success');
        } else {
            throw new Error(data.message || 'Failed to load intents');
        }
        
    } catch (error) {
        console.error('Error loading intents:', error);
        
        // Show user-friendly error
        const errorMessage = error.message.includes('mock data') 
            ? 'Using demo data. ' + error.message
            : 'Error: ' + error.message;
            
        showNotification(errorMessage, 'error');
        
        // Render mock data if available
        if (allIntents && allIntents.length > 0) {
            renderIntents();
        } else {
            // Show error in table
            const tbody = document.getElementById('intentsTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 24px; color: var(--warning);"></i>
                            <p style="margin-top: 10px;">${errorMessage}</p>
                            <p style="font-size: 0.9rem; margin-top: 5px;">Your Dialogflow connection is working, but there's an issue with the API response.</p>
                            <button class="btn-primary" onclick="loadIntents()" style="margin-top: 10px;">
                                <i class="fas fa-redo"></i> Try Again
                            </button>
                        </td>
                    </tr>
                `;
            }
        }
    } finally {
        const syncBtn = document.getElementById('syncButton');
        if (syncBtn) {
            syncBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync Intents';
            syncBtn.disabled = false;
        }
    }
}

// Update the updateStats function
function updateStats() {
    if (!allIntents || allIntents.length === 0) return;
    
    // Update basic stats
    document.getElementById('totalIntents').textContent = allIntents.length;
    
    // Calculate additional stats
    let webhookEnabled = 0;
    let fallbackIntents = 0;
    
    allIntents.forEach(intent => {
        if (intent.webhook_state === 'WEBHOOK_STATE_ENABLED') {
            webhookEnabled++;
        }
        if (intent.is_fallback) {
            fallbackIntents++;
        }
    });
    
    // Update phrases and responses counts (these might not be in basic view)
    document.getElementById('totalPhrases').textContent = '—';
    document.getElementById('totalResponses').textContent = '—';
    
    const updateStatus = document.getElementById('updateStatus');
    if (updateStatus) {
        updateStatus.textContent = `${webhookEnabled} webhook enabled, ${fallbackIntents} fallback`;
    }
    
    document.getElementById('lastUpdated').textContent = 'Just now';
}

// Add a function to load more intents
async function loadMoreIntents() {
    const pagination = document.getElementById('intentsPagination');
    if (!pagination || pagination.style.display === 'none') return;
    
    try {
        // Show loading
        const tbody = document.getElementById('intentsTableBody');
        const loadingRow = document.createElement('tr');
        loadingRow.id = 'loadingMoreRow';
        loadingRow.innerHTML = `
            <td colspan="6" style="text-align: center; padding: 20px;">
                <i class="fas fa-spinner fa-spin" style="color: var(--primary);"></i>
                <span style="margin-left: 10px; color: #666;">Loading more intents...</span>
            </td>
        `;
        tbody.appendChild(loadingRow);
        
        // Load next page
        currentPage++;
        renderIntents();
        
        // Remove loading row
        setTimeout(() => {
            const loadingMoreRow = document.getElementById('loadingMoreRow');
            if (loadingMoreRow) loadingMoreRow.remove();
        }, 500);
        
    } catch (error) {
        console.error('Error loading more intents:', error);
        showNotification('Error loading more intents', 'error');
    }
}

// Sync intents
async function syncIntents() {
    const syncBtn = document.getElementById('syncButton');
    if (syncBtn) {
        syncBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
        syncBtn.disabled = true;
    }
    
    try {
        await loadIntents();
        showNotification('Intents synced successfully!', 'success');
    } catch (error) {
        showNotification('Failed to sync intents: ' + error.message, 'error');
    } finally {
        if (syncBtn) {
            syncBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync Intents';
            syncBtn.disabled = false;
        }
    }
}

// Render intents table
function renderIntents() {
    const tbody = document.getElementById('intentsTableBody');
    const pagination = document.getElementById('intentsPagination');
    
    if (!tbody) return;
    
    // Calculate pagination
    const startIndex = (currentPage - 1) * intentsPerPage;
    const endIndex = startIndex + intentsPerPage;
    const pageIntents = filteredIntents.slice(startIndex, endIndex);
    const totalPages = Math.ceil(filteredIntents.length / intentsPerPage);
    
    // Clear table
    tbody.innerHTML = '';
    
    if (pageIntents.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 24px; color: var(--gray);"></i>
                    <p style="margin-top: 10px;">No intents found. Create your first intent!</p>
                </td>
            </tr>
        `;
        if (pagination) pagination.style.display = 'none';
        return;
    }
    
    // Add intents to table
    pageIntents.forEach(intent => {
        const isFallback = intent.is_fallback || 
                          intent.display_name.toLowerCase().includes('fallback') ||
                          intent.display_name === 'Default Fallback Intent';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div style="font-weight: 500; color: var(--primary);">${intent.display_name}</div>
                <div style="font-size: 0.8rem; color: #666; margin-top: 4px;">
                    <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-size: 0.75rem;">
                        ${intent.id.substring(0, 8)}...
                    </code>
                </div>
            </td>
            <td>
                <div class="badge" style="background: #e3f2fd; color: #1976d2; padding: 6px 12px; border-radius: 12px; display: inline-block;">
                    <i class="fas fa-comment-dots" style="margin-right: 4px;"></i>
                    ${intent.training_phrases.length} phrases
                </div>
                <div style="font-size: 0.8rem; color: #666; margin-top: 4px;">
                    ${intent.training_phrases.slice(0, 2).map(p => `• ${truncateText(p, 30)}`).join('<br>')}
                    ${intent.training_phrases.length > 2 ? '<br>+ ' + (intent.training_phrases.length - 2) + ' more' : ''}
                </div>
            </td>
            <td>
                <div class="badge" style="background: #e8f5e8; color: #2e7d32; padding: 6px 12px; border-radius: 12px; display: inline-block;">
                    <i class="fas fa-reply" style="margin-right: 4px;"></i>
                    ${intent.responses.length} responses
                </div>
                <div style="font-size: 0.8rem; color: #666; margin-top: 4px;">
                    ${truncateText(intent.responses[0] || 'No response', 50)}
                </div>
            </td>
            <td>
                ${intent.webhook_state === 'WEBHOOK_STATE_ENABLED' 
                    ? '<span class="badge" style="background: #4caf50; color: white; padding: 6px 12px; border-radius: 12px; display: inline-block;">Webhook</span>'
                    : '<span class="badge" style="background: #f5f5f5; color: #666; padding: 6px 12px; border-radius: 12px; display: inline-block;">Disabled</span>'}
            </td>
            <td>
                <div>${intent.updated_at ? formatDate(intent.updated_at) : 'N/A'}</div>
                <div style="font-size: 0.8rem; color: #666;">
                    ${intent.created_at ? formatDate(intent.created_at, true) : ''}
                </div>
            </td>
            <td>
                <div class="action-buttons-cell">
                    <button class="btn-action btn-view" onclick="viewIntent('${intent.id}')" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-action btn-edit" onclick="editIntentModal('${intent.id}')" title="Edit Intent">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action btn-test" onclick="quickTestIntent('${intent.display_name}')" title="Quick Test" style="background: #2196f3; color: white;">
                        <i class="fas fa-play"></i>
                    </button>
                    ${!isFallback ? `
                    <button class="btn-action btn-delete" onclick="deleteIntent('${intent.id}', '${intent.display_name}')" title="Delete Intent">
                        <i class="fas fa-trash"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    // Update pagination
    if (pagination) {
        if (totalPages > 1) {
            pagination.style.display = 'flex';
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
        } else {
            pagination.style.display = 'none';
        }
    }
}

// Helper function to truncate text
function truncateText(text, maxLength) {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Helper function to format date
function formatDate(dateString, timeOnly = false) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (timeOnly) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    return date.toLocaleDateString();
}

// Update statistics
function updateStats() {
    let totalPhrases = 0;
    let totalResponses = 0;
    let latestUpdate = null;
    
    allIntents.forEach(intent => {
        totalPhrases += intent.training_phrases.length;
        totalResponses += intent.responses.length;
        
        if (intent.updated_at) {
            const updateDate = new Date(intent.updated_at);
            if (!latestUpdate || updateDate > latestUpdate) {
                latestUpdate = updateDate;
            }
        }
    });
    
    // Update stats cards
    document.getElementById('totalIntents').textContent = allIntents.length;
    document.getElementById('totalPhrases').textContent = totalPhrases;
    document.getElementById('totalResponses').textContent = totalResponses;
    
    if (latestUpdate) {
        document.getElementById('lastUpdated').textContent = latestUpdate.toLocaleDateString();
        document.getElementById('updateStatus').textContent = 'Last sync: ' + latestUpdate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
}

// Search intents
function searchIntents(searchTerm) {
    if (!searchTerm) {
        filteredIntents = [...allIntents];
    } else {
        const term = searchTerm.toLowerCase();
        filteredIntents = allIntents.filter(intent => 
            intent.display_name.toLowerCase().includes(term) ||
            intent.training_phrases.some(phrase => phrase.toLowerCase().includes(term)) ||
            intent.responses.some(response => response.toLowerCase().includes(term))
        );
    }
    
    currentPage = 1;
    filterIntents();
}

// Filter intents by type
function filterIntents() {
    const typeFilter = document.getElementById('typeFilter').value;
    
    if (typeFilter === 'all') {
        // Already filtered by search
    } else if (typeFilter === 'webhook') {
        filteredIntents = filteredIntents.filter(intent => 
            intent.webhook_state === 'WEBHOOK_STATE_ENABLED'
        );
    } else if (typeFilter === 'fallback') {
        filteredIntents = filteredIntents.filter(intent => 
            intent.is_fallback || intent.display_name.toLowerCase().includes('fallback')
        );
    } else if (typeFilter === 'custom') {
        filteredIntents = filteredIntents.filter(intent => 
            !intent.is_fallback && !intent.display_name.toLowerCase().includes('fallback')
        );
    }
    
    sortIntents();
}

// Sort intents
function sortIntents() {
    const sortBy = document.getElementById('sortFilter').value;
    
    filteredIntents.sort((a, b) => {
        switch (sortBy) {
            case 'name_asc':
                return a.display_name.localeCompare(b.display_name);
            case 'name_desc':
                return b.display_name.localeCompare(a.display_name);
            case 'updated_desc':
                const dateA = a.updated_at ? new Date(a.updated_at) : new Date(0);
                const dateB = b.updated_at ? new Date(b.updated_at) : new Date(0);
                return dateB - dateA;
            case 'created_desc':
                const createdA = a.created_at ? new Date(a.created_at) : new Date(0);
                const createdB = b.created_at ? new Date(b.created_at) : new Date(0);
                return createdB - createdA;
            default:
                return 0;
        }
    });
    
    renderIntents();
}

// Pagination functions
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderIntents();
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredIntents.length / intentsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderIntents();
    }
}

// Create Intent Modal
function createIntentModal() {
    const modalHtml = `
        <div class="modal" id="createIntentModal">
            <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h3><i class="fas fa-plus-circle" style="margin-right: 10px; color: var(--secondary);"></i>Create New Intent</h3>
                    <button class="close-modal" onclick="closeModal('createIntentModal')">×</button>
                </div>
                <div class="modal-body">
                    <form id="createIntentForm">
                        <div class="form-group">
                            <label for="intentDisplayName"><i class="fas fa-tag"></i> Intent Display Name *</label>
                            <input type="text" id="intentDisplayName" required 
                                   placeholder="e.g., Leave Policy Inquiry, Salary Information, etc."
                                   style="width: 100%; padding: 12px; font-size: 1rem;">
                            <small style="color: #666; display: block; margin-top: 5px;">
                                Use a clear, descriptive name that represents the intent's purpose
                            </small>
                        </div>
                        
                        <div class="form-section" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                            <h4 style="color: var(--primary); margin-bottom: 15px;">
                                <i class="fas fa-comment-dots"></i> Training Phrases
                            </h4>
                            <small style="color: #666; display: block; margin-bottom: 15px;">
                                Add different ways users might ask about this topic. The more variations you add, the better the intent matching.
                            </small>
                            
                            <div id="trainingPhrasesContainer" style="margin-bottom: 15px;">
                                <div class="training-phrase-item" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                    <div style="flex: 1; position: relative;">
                                        <input type="text" class="training-phrase" 
                                               placeholder="e.g., How do I apply for leave?" 
                                               style="width: 100%; padding: 12px 12px 12px 40px;">
                                        <i class="fas fa-comment" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                                    </div>
                                    <button type="button" class="btn-danger" 
                                            onclick="removeTrainingPhrase(this)" 
                                            style="padding: 12px 15px; border-radius: 6px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="button" class="btn-secondary" 
                                    onclick="addTrainingPhrase()" 
                                    style="padding: 10px 20px; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Add Another Training Phrase
                            </button>
                        </div>
                        
                        <div class="form-section" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                            <h4 style="color: var(--primary); margin-bottom: 15px;">
                                <i class="fas fa-reply"></i> Response Messages
                            </h4>
                            <small style="color: #666; display: block; margin-bottom: 15px;">
                                Add responses that the bot will give when this intent is matched. You can add multiple variations.
                            </small>
                            
                            <div id="responsesContainer" style="margin-bottom: 15px;">
                                <div class="response-item" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <div style="flex: 1; position: relative;">
                                        <textarea class="response-text" rows="4" 
                                                  placeholder="Enter the bot's response message here. You can include formatting and links."
                                                  style="width: 100%; padding: 12px 12px 12px 40px; resize: vertical; font-family: inherit;"></textarea>
                                        <i class="fas fa-reply" style="position: absolute; left: 15px; top: 15px; color: var(--gray);"></i>
                                    </div>
                                    <button type="button" class="btn-danger" 
                                            onclick="removeResponse(this)" 
                                            style="padding: 12px 15px; border-radius: 6px; align-self: flex-start;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="button" class="btn-secondary" 
                                    onclick="addResponse()" 
                                    style="padding: 10px 20px; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Add Another Response
                            </button>
                        </div>
                        
                        <div class="form-section" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                            <h4 style="color: var(--primary); margin-bottom: 15px;">
                                <i class="fas fa-cogs"></i> Advanced Settings
                            </h4>
                            
                            <div class="form-group">
                                <label for="webhookState"><i class="fas fa-plug"></i> Webhook Integration</label>
                                <select id="webhookState" style="width: 100%; padding: 12px;">
                                    <option value="WEBHOOK_STATE_UNSPECIFIED">No webhook (Direct Response)</option>
                                    <option value="WEBHOOK_STATE_ENABLED">Enable webhook (Custom Processing)</option>
                                </select>
                                <small style="color: #666; display: block; margin-top: 5px;">
                                    Enable webhook if you need custom logic or database lookups for this intent
                                </small>
                            </div>
                            
                            <div class="form-group" style="margin-top: 15px;">
                                <label><i class="fas fa-robot"></i> AI Training</label>
                                <div style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
                                    <input type="checkbox" id="mlEnabled" checked style="transform: scale(1.2);">
                                    <label for="mlEnabled" style="margin: 0;">Enable machine learning for this intent</label>
                                </div>
                                <small style="color: #666; display: block; margin-top: 5px;">
                                    Allow Dialogflow to learn from user interactions and improve matching
                                </small>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                            <button type="submit" class="btn-primary" style="padding: 12px 24px; font-size: 1rem;">
                                <i class="fas fa-save"></i> Create Intent
                            </button>
                            <button type="button" class="btn-secondary" onclick="closeModal('createIntentModal')" style="padding: 12px 24px;">
                                Cancel
                            </button>
                            <button type="button" class="btn-secondary" onclick="resetCreateForm()" style="padding: 12px 24px; margin-left: auto;">
                                <i class="fas fa-redo"></i> Reset Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to body
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer.firstChild);
    
    // Show modal
    setTimeout(() => {
        document.getElementById('createIntentModal').classList.add('active');
    }, 10);
    
    // Add form submission handler
    document.getElementById('createIntentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const displayName = document.getElementById('intentDisplayName').value.trim();
        const trainingPhrases = Array.from(document.getElementsByClassName('training-phrase'))
            .map(input => input.value.trim())
            .filter(text => text !== '');
        
        const responses = Array.from(document.getElementsByClassName('response-text'))
            .map(textarea => textarea.value.trim())
            .filter(text => text !== '');
        
        const webhookState = document.getElementById('webhookState').value;
        const mlEnabled = document.getElementById('mlEnabled').checked;
        
        // Validation
        if (!displayName) {
            showNotification('Please enter an intent name', 'error');
            return;
        }
        
        if (trainingPhrases.length === 0) {
            showNotification('Please add at least one training phrase', 'error');
            return;
        }
        
        if (responses.length === 0) {
            showNotification('Please add at least one response', 'error');
            return;
        }
        
        // Disable submit button
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('/admin/dialogflow/intents/api/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    display_name: displayName,
                    training_phrases: trainingPhrases,
                    responses: responses,
                    webhook_state: webhookState,
                    ml_enabled: mlEnabled
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Intent created successfully!', 'success');
                closeModal('createIntentModal');
                loadIntents(); // Refresh the list
            } else {
                showNotification('Failed to create intent: ' + data.message, 'error');
            }
        } catch (error) {
            showNotification('Error creating intent: ' + error.message, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// Helper functions for training phrases and responses
function addTrainingPhrase() {
    const container = document.getElementById('trainingPhrasesContainer');
    const newPhrase = document.createElement('div');
    newPhrase.className = 'training-phrase-item';
    newPhrase.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: center;';
    newPhrase.innerHTML = `
        <div style="flex: 1; position: relative;">
            <input type="text" class="training-phrase" 
                   placeholder="e.g., What is the leave policy?" 
                   style="width: 100%; padding: 12px 12px 12px 40px;">
            <i class="fas fa-comment" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
        </div>
        <button type="button" class="btn-danger" 
                onclick="removeTrainingPhrase(this)" 
                style="padding: 12px 15px; border-radius: 6px;">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newPhrase);
}

function removeTrainingPhrase(button) {
    const container = document.getElementById('trainingPhrasesContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        showNotification('At least one training phrase is required', 'error');
    }
}

function addResponse() {
    const container = document.getElementById('responsesContainer');
    const newResponse = document.createElement('div');
    newResponse.className = 'response-item';
    newResponse.style.cssText = 'display: flex; gap: 10px; margin-bottom: 15px;';
    newResponse.innerHTML = `
        <div style="flex: 1; position: relative;">
            <textarea class="response-text" rows="4" 
                      placeholder="Enter the bot's response message here..." 
                      style="width: 100%; padding: 12px 12px 12px 40px; resize: vertical; font-family: inherit;"></textarea>
            <i class="fas fa-reply" style="position: absolute; left: 15px; top: 15px; color: var(--gray);"></i>
        </div>
        <button type="button" class="btn-danger" 
                onclick="removeResponse(this)" 
                style="padding: 12px 15px; border-radius: 6px; align-self: flex-start;">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newResponse);
}

function removeResponse(button) {
    const container = document.getElementById('responsesContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        showNotification('At least one response is required', 'error');
    }
}

function resetCreateForm() {
    if (confirm('Reset the form? All entered data will be lost.')) {
        document.getElementById('intentDisplayName').value = '';
        document.getElementById('trainingPhrasesContainer').innerHTML = `
            <div class="training-phrase-item" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                <div style="flex: 1; position: relative;">
                    <input type="text" class="training-phrase" 
                           placeholder="e.g., How do I apply for leave?" 
                           style="width: 100%; padding: 12px 12px 12px 40px;">
                    <i class="fas fa-comment" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                </div>
                <button type="button" class="btn-danger" 
                        onclick="removeTrainingPhrase(this)" 
                        style="padding: 12px 15px; border-radius: 6px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        document.getElementById('responsesContainer').innerHTML = `
            <div class="response-item" style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1; position: relative;">
                    <textarea class="response-text" rows="4" 
                              placeholder="Enter the bot's response message here..." 
                              style="width: 100%; padding: 12px 12px 12px 40px; resize: vertical; font-family: inherit;"></textarea>
                    <i class="fas fa-reply" style="position: absolute; left: 15px; top: 15px; color: var(--gray);"></i>
                </div>
                <button type="button" class="btn-danger" 
                        onclick="removeResponse(this)" 
                        style="padding: 12px 15px; border-radius: 6px; align-self: flex-start;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        document.getElementById('webhookState').value = 'WEBHOOK_STATE_UNSPECIFIED';
        document.getElementById('mlEnabled').checked = true;
        showNotification('Form reset successfully', 'info');
    }
}

// View intent details
async function viewIntent(intentId) {
    try {
        const response = await fetch(`/admin/dialogflow/intents/api/${intentId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const intent = data.intent;
            showIntentDetailsModal(intent);
        } else {
            showNotification('Failed to load intent: ' + data.message, 'error');
        }
    } catch (error) {
        showNotification('Error loading intent: ' + error.message, 'error');
    }
}

// Show intent details modal
function showIntentDetailsModal(intent) {
    const modalHtml = `
        <div class="modal" id="viewIntentModal">
            <div class="modal-content" style="max-width: 1000px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                    <h3 style="color: white; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-robot"></i> ${intent.display_name}
                    </h3>
                    <button class="close-modal" onclick="closeModal('viewIntentModal')" style="color: white;">×</button>
                </div>
                <div class="modal-body">
                    <!-- Intent Info Cards -->
                    <div class="dashboard-cards" style="margin-bottom: 25px; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                        <div class="card" style="text-align: center; padding: 15px;">
                            <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Intent ID</div>
                            <div style="font-family: monospace; font-size: 0.8rem; word-break: break-all; color: var(--primary);">
                                ${intent.id}
                            </div>
                        </div>
                        <div class="card" style="text-align: center; padding: 15px;">
                            <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Webhook</div>
                            <div style="font-weight: 500;">
                                ${intent.webhook_state === 'WEBHOOK_STATE_ENABLED' 
                                    ? '<span style="color: #4caf50;"><i class="fas fa-check-circle"></i> Enabled</span>'
                                    : '<span style="color: #666;"><i class="fas fa-times-circle"></i> Disabled</span>'}
                            </div>
                        </div>
                        <div class="card" style="text-align: center; padding: 15px;">
                            <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Created</div>
                            <div>${intent.created_at ? new Date(intent.created_at).toLocaleDateString() : 'N/A'}</div>
                        </div>
                        <div class="card" style="text-align: center; padding: 15px;">
                            <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Last Updated</div>
                            <div>${intent.updated_at ? new Date(intent.updated_at).toLocaleDateString() : 'N/A'}</div>
                        </div>
                    </div>
                    
                    <!-- Training Phrases -->
                    <div class="data-table" style="margin-bottom: 25px;">
                        <h4 style="color: var(--primary); margin-bottom: 15px;">
                            <i class="fas fa-comment-dots"></i> Training Phrases (${intent.training_phrases.length})
                        </h4>
                        <div style="max-height: 300px; overflow-y: auto; background: #f9f9f9; border-radius: 8px; padding: 15px;">
                            ${intent.training_phrases.map((phrase, index) => `
                                <div style="padding: 12px; background: white; margin-bottom: 10px; border-radius: 6px; border-left: 4px solid var(--primary);">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500; color: var(--primary); margin-bottom: 4px;">
                                                Example ${index + 1}
                                            </div>
                                            <div style="color: #333;">${phrase}</div>
                                        </div>
                                        <button onclick="copyToClipboard('${phrase.replace(/'/g, "\\'")}')" 
                                                style="background: none; border: none; color: var(--gray); cursor: pointer; padding: 5px;"
                                                title="Copy to clipboard">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <!-- Responses -->
                    <div class="data-table" style="margin-bottom: 25px;">
                        <h4 style="color: var(--primary); margin-bottom: 15px;">
                            <i class="fas fa-reply"></i> Responses (${intent.responses.length})
                        </h4>
                        <div style="max-height: 400px; overflow-y: auto;">
                            ${intent.responses.map((response, index) => `
                                <div style="padding: 20px; background: #f8fdf9; margin-bottom: 15px; border-radius: 8px; border: 1px solid #e0efe5;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                        <div style="font-weight: 500; color: var(--primary);">
                                            <i class="fas fa-robot" style="margin-right: 8px;"></i>
                                            Response ${index + 1}
                                        </div>
                                        <div>
                                            <button onclick="copyToClipboard('${response.replace(/'/g, "\\'").replace(/\n/g, '\\n')}')" 
                                                    style="background: none; border: none; color: var(--gray); cursor: pointer; padding: 5px; margin-right: 5px;"
                                                    title="Copy to clipboard">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button onclick="testResponse('${intent.display_name}', '${response.replace(/'/g, "\\'").replace(/\n/g, '\\n')}')" 
                                                    style="background: none; border: none; color: var(--gray); cursor: pointer; padding: 5px;"
                                                    title="Test this response">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div style="white-space: pre-wrap; color: #333; line-height: 1.6; font-size: 0.95rem;">
                                        ${response}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <!-- Parameters (if any) -->
                    ${intent.parameters && intent.parameters.length > 0 ? `
                    <div class="data-table" style="margin-bottom: 25px;">
                        <h4 style="color: var(--primary); margin-bottom: 15px;">
                            <i class="fas fa-list-ul"></i> Parameters (${intent.parameters.length})
                        </h4>
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Parameter Name</th>
                                    <th>Entity Type</th>
                                    <th>Required</th>
                                    <th>Default Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${intent.parameters.map(param => `
                                    <tr>
                                        <td><strong>${param.name}</strong></td>
                                        <td><code style="background: #f5f5f5; padding: 3px 6px; border-radius: 3px;">${param.entity_type}</code></td>
                                        <td>
                                            ${param.mandatory 
                                                ? '<span style="color: #4caf50;"><i class="fas fa-check-circle"></i> Required</span>'
                                                : '<span style="color: #666;"><i class="fas fa-times-circle"></i> Optional</span>'}
                                        </td>
                                        <td>${param.value || '—'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    ` : ''}
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                        <button onclick="editIntentModal('${intent.id}'); closeModal('viewIntentModal');" 
                                class="btn-primary" style="padding: 12px 24px;">
                            <i class="fas fa-edit"></i> Edit Intent
                        </button>
                        <button onclick="quickTestIntent('${intent.display_name}'); closeModal('viewIntentModal');" 
                                class="btn-secondary" style="padding: 12px 24px;">
                            <i class="fas fa-play"></i> Quick Test
                        </button>
                        ${!intent.is_fallback ? `
                        <button onclick="deleteIntent('${intent.id}', '${intent.display_name}'); closeModal('viewIntentModal');" 
                                class="btn-danger" style="padding: 12px 24px; margin-left: auto;">
                            <i class="fas fa-trash"></i> Delete Intent
                        </button>
                        ` : ''}
                        <button onclick="closeModal('viewIntentModal')" 
                                class="btn-secondary" style="padding: 12px 24px;">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer.firstChild);
    
    setTimeout(() => {
        document.getElementById('viewIntentModal').classList.add('active');
    }, 10);
}

// Edit intent modal
async function editIntentModal(intentId) {
    try {
        const response = await fetch(`/admin/dialogflow/intents/api/${intentId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (!data.success) {
            showNotification('Failed to load intent: ' + data.message, 'error');
            return;
        }
        
        const intent = data.intent;
        
        const modalHtml = `
            <div class="modal" id="editIntentModal">
                <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
                    <div class="modal-header">
                        <h3><i class="fas fa-edit" style="margin-right: 10px; color: var(--warning);"></i>Edit Intent: ${intent.display_name}</h3>
                        <button class="close-modal" onclick="closeModal('editIntentModal')">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="editIntentForm">
                            <input type="hidden" id="editIntentId" value="${intent.id}">
                            
                            <div class="form-group">
                                <label for="editIntentDisplayName"><i class="fas fa-tag"></i> Intent Display Name *</label>
                                <input type="text" id="editIntentDisplayName" 
                                       value="${intent.display_name.replace(/"/g, '&quot;')}"
                                       required style="width: 100%; padding: 12px;">
                            </div>
                            
                            <div class="form-section" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                                <h4 style="color: var(--primary); margin-bottom: 15px;">
                                    <i class="fas fa-comment-dots"></i> Training Phrases (${intent.training_phrases.length})
                                </h4>
                                
                                <div id="editTrainingPhrasesContainer" style="margin-bottom: 15px;">
                                    ${intent.training_phrases.map(phrase => `
                                        <div class="training-phrase-item" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                            <div style="flex: 1; position: relative;">
                                                <input type="text" class="training-phrase" 
                                                       value="${phrase.replace(/"/g, '&quot;')}"
                                                       style="width: 100%; padding: 12px 12px 12px 40px;">
                                                <i class="fas fa-comment" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                                            </div>
                                            <button type="button" class="btn-danger" 
                                                    onclick="removeTrainingPhraseEdit(this)" 
                                                    style="padding: 12px 15px; border-radius: 6px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                                
                                <button type="button" class="btn-secondary" 
                                        onclick="addTrainingPhraseEdit()" 
                                        style="padding: 10px 20px; border-radius: 6px;">
                                    <i class="fas fa-plus"></i> Add Training Phrase
                                </button>
                            </div>
                            
                            <div class="form-section" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                                <h4 style="color: var(--primary); margin-bottom: 15px;">
                                    <i class="fas fa-reply"></i> Responses (${intent.responses.length})
                                </h4>
                                
                                <div id="editResponsesContainer" style="margin-bottom: 15px;">
                                    ${intent.responses.map(response => `
                                        <div class="response-item" style="display: flex; gap: 10px; margin-bottom: 15px;">
                                            <div style="flex: 1; position: relative;">
                                                <textarea class="response-text" rows="4" 
                                                          style="width: 100%; padding: 12px 12px 12px 40px; resize: vertical; font-family: inherit;">${response}</textarea>
                                                <i class="fas fa-reply" style="position: absolute; left: 15px; top: 15px; color: var(--gray);"></i>
                                            </div>
                                            <button type="button" class="btn-danger" 
                                                    onclick="removeResponseEdit(this)" 
                                                    style="padding: 12px 15px; border-radius: 6px; align-self: flex-start;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                                
                                <button type="button" class="btn-secondary" 
                                        onclick="addResponseEdit()" 
                                        style="padding: 10px 20px; border-radius: 6px;">
                                    <i class="fas fa-plus"></i> Add Response
                                </button>
                            </div>
                            
                            <div class="form-section" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                                <h4 style="color: var(--primary); margin-bottom: 15px;">
                                    <i class="fas fa-cogs"></i> Advanced Settings
                                </h4>
                                
                                <div class="form-group">
                                    <label for="editWebhookState"><i class="fas fa-plug"></i> Webhook Integration</label>
                                    <select id="editWebhookState" style="width: 100%; padding: 12px;">
                                        <option value="WEBHOOK_STATE_UNSPECIFIED" ${intent.webhook_state === 'WEBHOOK_STATE_UNSPECIFIED' ? 'selected' : ''}>No webhook (Direct Response)</option>
                                        <option value="WEBHOOK_STATE_ENABLED" ${intent.webhook_state === 'WEBHOOK_STATE_ENABLED' ? 'selected' : ''}>Enable webhook (Custom Processing)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                                <button type="submit" class="btn-primary" style="padding: 12px 24px;">
                                    <i class="fas fa-save"></i> Update Intent
                                </button>
                                <button type="button" class="btn-secondary" onclick="closeModal('editIntentModal')" style="padding: 12px 24px;">
                                    Cancel
                                </button>
                                <button type="button" class="btn-secondary" onclick="viewIntent('${intent.id}'); closeModal('editIntentModal');" style="padding: 12px 24px; margin-left: auto;">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        const modalContainer = document.createElement('div');
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer.firstChild);
        
        setTimeout(() => {
            document.getElementById('editIntentModal').classList.add('active');
        }, 10);
        
        // Add form submission handler
        document.getElementById('editIntentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const intentId = document.getElementById('editIntentId').value;
            const displayName = document.getElementById('editIntentDisplayName').value.trim();
            const trainingPhrases = Array.from(document.querySelectorAll('#editTrainingPhrasesContainer .training-phrase'))
                .map(input => input.value.trim())
                .filter(text => text !== '');
            
            const responses = Array.from(document.querySelectorAll('#editResponsesContainer .response-text'))
                .map(textarea => textarea.value.trim())
                .filter(text => text !== '');
            
            const webhookState = document.getElementById('editWebhookState').value;
            
            // Validation
            if (!displayName) {
                showNotification('Please enter an intent name', 'error');
                return;
            }
            
            if (trainingPhrases.length === 0) {
                showNotification('Please add at least one training phrase', 'error');
                return;
            }
            
            if (responses.length === 0) {
                showNotification('Please add at least one response', 'error');
                return;
            }
            
            // Disable submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch(`/admin/dialogflow/intents/api/${intentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        display_name: displayName,
                        training_phrases: trainingPhrases,
                        responses: responses,
                        webhook_state: webhookState
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Intent updated successfully!', 'success');
                    closeModal('editIntentModal');
                    loadIntents(); // Refresh the list
                } else {
                    showNotification('Failed to update intent: ' + data.message, 'error');
                }
            } catch (error) {
                showNotification('Error updating intent: ' + error.message, 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
        
    } catch (error) {
        showNotification('Error loading intent: ' + error.message, 'error');
    }
}

// Helper functions for edit modal
function addTrainingPhraseEdit() {
    const container = document.getElementById('editTrainingPhrasesContainer');
    const newPhrase = document.createElement('div');
    newPhrase.className = 'training-phrase-item';
    newPhrase.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: center;';
    newPhrase.innerHTML = `
        <div style="flex: 1; position: relative;">
            <input type="text" class="training-phrase" 
                   placeholder="Add new training phrase..." 
                   style="width: 100%; padding: 12px 12px 12px 40px;">
            <i class="fas fa-comment" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
        </div>
        <button type="button" class="btn-danger" 
                onclick="removeTrainingPhraseEdit(this)" 
                style="padding: 12px 15px; border-radius: 6px;">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newPhrase);
}

function removeTrainingPhraseEdit(button) {
    const container = document.getElementById('editTrainingPhrasesContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        showNotification('At least one training phrase is required', 'error');
    }
}

function addResponseEdit() {
    const container = document.getElementById('editResponsesContainer');
    const newResponse = document.createElement('div');
    newResponse.className = 'response-item';
    newResponse.style.cssText = 'display: flex; gap: 10px; margin-bottom: 15px;';
    newResponse.innerHTML = `
        <div style="flex: 1; position: relative;">
            <textarea class="response-text" rows="4" 
                      placeholder="Add new response..." 
                      style="width: 100%; padding: 12px 12px 12px 40px; resize: vertical; font-family: inherit;"></textarea>
            <i class="fas fa-reply" style="position: absolute; left: 15px; top: 15px; color: var(--gray);"></i>
        </div>
        <button type="button" class="btn-danger" 
                onclick="removeResponseEdit(this)" 
                style="padding: 12px 15px; border-radius: 6px; align-self: flex-start;">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newResponse);
}

function removeResponseEdit(button) {
    const container = document.getElementById('editResponsesContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        showNotification('At least one response is required', 'error');
    }
}

// Delete intent
async function deleteIntent(intentId, intentName) {
    if (!confirm(`Are you sure you want to delete the intent "${intentName}"?\n\nThis action cannot be undone and may affect your chatbot's performance.`)) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/dialogflow/intents/api/${intentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(`Intent "${intentName}" deleted successfully!`, 'success');
            loadIntents(); // Refresh the list
        } else {
            showNotification('Failed to delete intent: ' + data.message, 'error');
        }
    } catch (error) {
        showNotification('Error deleting intent: ' + error.message, 'error');
    }
}

// Test intent matching
async function testIntent() {
    const query = document.getElementById('testQuery').value.trim();
    
    if (!query) {
        showNotification('Please enter a query to test', 'error');
        return;
    }
    
    const testBtn = document.getElementById('testButton');
    const originalText = testBtn.innerHTML;
    testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    testBtn.disabled = true;
    
    try {
        const response = await fetch('/admin/dialogflow/intents/api/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                query: query,
                session_id: 'test-session-' + Date.now()
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const result = data.result;
            const resultContent = document.getElementById('testResultContent');
            const testResultDiv = document.getElementById('testResult');
            
            resultContent.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5;">
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Test Query</div>
                        <div style="font-weight: 500; font-size: 1.1rem; color: var(--primary);">"${query}"</div>
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5;">
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Matched Intent</div>
                        <div style="font-weight: 600; font-size: 1.2rem; color: ${result.matched_intent ? 'var(--success)' : 'var(--danger)'};">
                            ${result.matched_intent || 'No intent matched'}
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5;">
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Confidence Score</div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="font-weight: 600; font-size: 1.5rem; color: ${result.confidence > 0.7 ? 'var(--success)' : result.confidence > 0.4 ? 'var(--warning)' : 'var(--danger)'};">
                                ${(result.confidence * 100).toFixed(1)}%
                            </div>
                            <div style="flex: 1;">
                                <div style="height: 8px; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                                    <div style="height: 100%; width: ${result.confidence * 100}%; 
                                                background: ${result.confidence > 0.7 ? 'var(--success)' : result.confidence > 0.4 ? 'var(--warning)' : 'var(--danger)'};">
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 0.8rem; color: var(--gray);">
                                    <span>0%</span>
                                    <span>100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5;">
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Parameters Status</div>
                        <div style="font-weight: 500; font-size: 1.1rem; color: ${result.all_required_params_present ? 'var(--success)' : 'var(--warning)'};">
                            ${result.all_required_params_present ? 'All parameters present ✓' : 'Missing parameters'}
                        </div>
                    </div>
                </div>
                
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5; margin-bottom: 20px;">
                    <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Bot Response</div>
                    <div style="white-space: pre-wrap; color: #333; line-height: 1.6; padding: 15px; background: #f9f9f9; border-radius: 6px;">
                        ${result.response}
                    </div>
                </div>
                
                ${result.parameters && result.parameters !== '{}' ? `
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5;">
                    <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Extracted Parameters</div>
                    <pre style="margin: 0; padding: 15px; background: #f9f9f9; border-radius: 6px; overflow-x: auto; font-size: 0.9rem;">
${JSON.stringify(JSON.parse(result.parameters), null, 2)}
                    </pre>
                </div>
                ` : ''}
            `;
            
            testResultDiv.style.display = 'block';
            testResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
        } else {
            showNotification('Failed to test intent: ' + data.message, 'error');
        }
    } catch (error) {
        showNotification('Error testing intent: ' + error.message, 'error');
    } finally {
        testBtn.innerHTML = originalText;
        testBtn.disabled = false;
    }
}

// Quick test intent
function quickTestIntent(intentName) {
    const testQuery = document.getElementById('testQuery');
    if (testQuery) {
        // Use the first training phrase or create a generic test
        testQuery.value = `Test: ${intentName}`;
        toggleTestPanel(); // Ensure test panel is visible
        setTimeout(() => {
            testQuery.focus();
            testQuery.select();
        }, 100);
    }
}

// Toggle test panel
function toggleTestPanel() {
    const panel = document.getElementById('testPanel');
    const toggleBtn = document.getElementById('toggleTestBtn');
    
    if (panel.style.display === 'none' || !panel.style.display) {
        panel.style.display = 'block';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Test Panel';
    } else {
        panel.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Show Test Panel';
    }
}

// Export intents
async function exportIntents() {
    try {
        const response = await fetch('/admin/dialogflow/intents/api/export');
        const blob = await response.blob();
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `dialogflow-intents-backup-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showNotification('Intents exported successfully!', 'success');
    } catch (error) {
        showNotification('Error exporting intents: ' + error.message, 'error');
    }
}

// Import intents modal
function importIntentsModal() {
    const modalHtml = `
        <div class="modal" id="importIntentsModal">
            <div class="modal-content" style="max-width: 600px;">
                <div class="modal-header">
                    <h3><i class="fas fa-file-import"></i> Import Intents</h3>
                    <button class="close-modal" onclick="closeModal('importIntentsModal')">×</button>
                </div>
                <div class="modal-body">
                    <form id="importIntentsForm">
                        <div class="form-group">
                            <label for="intentsFile"><i class="fas fa-file-upload"></i> Select JSON File</label>
                            <div style="border: 2px dashed #e0efe5; border-radius: 8px; padding: 30px; text-align: center; margin-top: 10px; cursor: pointer;" 
                                 onclick="document.getElementById('fileInput').click()" id="dropZone">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--gray); margin-bottom: 15px;"></i>
                                <div style="font-weight: 500; color: var(--primary); margin-bottom: 8px;">
                                    Click to select or drag and drop
                                </div>
                                <div style="color: #666; font-size: 0.9rem;">
                                    Supports JSON files exported from Dialogflow
                                </div>
                                <input type="file" id="fileInput" accept=".json" style="display: none;" 
                                       onchange="handleFileSelect(this)">
                            </div>
                            <div id="selectedFile" style="display: none; margin-top: 15px; padding: 10px; background: #f8fdf9; border-radius: 6px; border: 1px solid #e0efe5;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <i class="fas fa-file-alt" style="color: var(--primary); margin-right: 8px;"></i>
                                        <span id="fileName"></span>
                                        <span id="fileSize" style="color: #666; margin-left: 8px;"></span>
                                    </div>
                                    <button type="button" onclick="clearFileSelection()" style="background: none; border: none; color: var(--danger); cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <label><i class="fas fa-cog"></i> Import Options</label>
                            <div style="margin-top: 10px;">
                                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                    <input type="radio" name="importOption" value="merge" checked>
                                    <span>Merge with existing intents (skip duplicates)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="importOption" value="overwrite">
                                    <span>Overwrite existing intents with same names</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="importOption" value="replace">
                                    <span>Replace all intents (clear existing first)</span>
                                </label>
                            </div>
                        </div>
                        
                        <div style="margin-top: 25px; padding: 15px; background: #f8fdf9; border-radius: 6px; border: 1px solid #e0efe5;">
                            <div style="font-weight: 500; color: var(--primary); margin-bottom: 8px;">
                                <i class="fas fa-info-circle"></i> File Format
                            </div>
                            <div style="font-size: 0.9rem; color: #666;">
                                The file should be a JSON array of intent objects. Each intent should have:
                                <ul style="margin: 8px 0 8px 20px;">
                                    <li>display_name</li>
                                    <li>training_phrases (array of strings)</li>
                                    <li>responses (array of strings)</li>
                                </ul>
                                You can export the current intents to see the exact format.
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn-primary" style="padding: 12px 24px;" id="importBtn">
                                <i class="fas fa-upload"></i> Import Intents
                            </button>
                            <button type="button" class="btn-secondary" onclick="closeModal('importIntentsModal')" style="padding: 12px 24px;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer.firstChild);
    
    setTimeout(() => {
        document.getElementById('importIntentsModal').classList.add('active');
    }, 10);
    
    // Add form submission handler
    document.getElementById('importIntentsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('fileInput');
        if (!fileInput.files.length) {
            showNotification('Please select a file to import', 'error');
            return;
        }
        
        const importOption = document.querySelector('input[name="importOption"]:checked').value;
        
        const formData = new FormData();
        formData.append('intents_file', fileInput.files[0]);
        formData.append('overwrite_existing', importOption === 'overwrite' || importOption === 'replace');
        if (importOption === 'replace') {
            formData.append('clear_existing', true);
        }
        
        const importBtn = document.getElementById('importBtn');
        const originalText = importBtn.innerHTML;
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
        importBtn.disabled = true;
        
        try {
            const response = await fetch('/admin/dialogflow/intents/api/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(`Successfully imported ${data.imported_count} intents!`, 'success');
                
                // Show any errors
                if (data.errors && data.errors.length > 0) {
                    const errorList = data.errors.map(e => `• ${e.intent}: ${e.error}`).join('\n');
                    alert(`Some intents failed to import:\n\n${errorList}`);
                }
                
                closeModal('importIntentsModal');
                loadIntents(); // Refresh the list
            } else {
                showNotification('Failed to import intents: ' + data.message, 'error');
            }
        } catch (error) {
            showNotification('Error importing intents: ' + error.message, 'error');
        } finally {
            importBtn.innerHTML = originalText;
            importBtn.disabled = false;
        }
    });
}

// Handle file selection
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const selectedFile = document.getElementById('selectedFile');
    const dropZone = document.getElementById('dropZone');
    
    fileName.textContent = file.name;
    fileSize.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
    selectedFile.style.display = 'block';
    dropZone.style.borderColor = 'var(--primary)';
    dropZone.style.background = 'rgba(74, 140, 94, 0.05)';
}

// Clear file selection
function clearFileSelection() {
    const fileInput = document.getElementById('fileInput');
    const selectedFile = document.getElementById('selectedFile');
    const dropZone = document.getElementById('dropZone');
    
    fileInput.value = '';
    selectedFile.style.display = 'none';
    dropZone.style.borderColor = '#e0efe5';
    dropZone.style.background = '';
}

// Test intent modal
function testIntentModal() {
    toggleTestPanel();
    const testQuery = document.getElementById('testQuery');
    if (testQuery) {
        testQuery.focus();
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showNotification('Failed to copy to clipboard', 'error');
    });
}

// Test specific response
function testResponse(intentName, response) {
    const testQuery = document.getElementById('testQuery');
    if (testQuery) {
        testQuery.value = `Test response for: ${intentName}`;
        toggleTestPanel();
        
        // Show the response in test panel
        setTimeout(() => {
            const resultContent = document.getElementById('testResultContent');
            const testResultDiv = document.getElementById('testResult');
            
            resultContent.innerHTML = `
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5;">
                    <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 8px;">Testing Response from: ${intentName}</div>
                    <div style="white-space: pre-wrap; color: #333; line-height: 1.6; padding: 15px; background: #f9f9f9; border-radius: 6px;">
                        ${response}
                    </div>
                </div>
            `;
            
            testResultDiv.style.display = 'block';
            testResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
}

// Open training guide
function openTrainingGuide() {
    const modalHtml = `
        <div class="modal" id="trainingGuideModal">
            <div class="modal-content" style="max-width: 800px;">
                <div class="modal-header">
                    <h3><i class="fas fa-graduation-cap"></i> Dialogflow Intent Training Guide</h3>
                    <button class="close-modal" onclick="closeModal('trainingGuideModal')">×</button>
                </div>
                <div class="modal-body">
                    <div style="max-height: 70vh; overflow-y: auto;">
                        <h4 style="color: var(--primary); margin-bottom: 15px;">Best Practices for Creating Intents</h4>
                        
                        <div style="margin-bottom: 25px;">
                            <h5><i class="fas fa-lightbulb" style="color: #ff9800;"></i> Training Phrases Tips</h5>
                            <ul style="padding-left: 20px; color: #555;">
                                <li><strong>Add variety:</strong> Include different ways users might ask the same question</li>
                                <li><strong>Be specific:</strong> Include specific examples rather than generic phrases</li>
                                <li><strong>Use parameters:</strong> Mark variable parts using @sys entities</li>
                                <li><strong>Include synonyms:</strong> Add different words with the same meaning</li>
                                <li><strong>Quantity matters:</strong> Aim for 10-20 training phrases per intent</li>
                            </ul>
                        </div>
                        
                        <div style="margin-bottom: 25px;">
                            <h5><i class="fas fa-reply" style="color: #4caf50;"></i> Response Writing Tips</h5>
                            <ul style="padding-left: 20px; color: #555;">
                                <li><strong>Be concise:</strong> Keep responses clear and to the point</li>
                                <li><strong>Use variables:</strong> Include $parameterName to use extracted values</li>
                                <li><strong>Add formatting:</strong> Use markdown for better readability</li>
                                <li><strong>Provide options:</strong> Suggest next steps or related questions</li>
                                <li><strong>Test responses:</strong> Ensure they sound natural and helpful</li>
                            </ul>
                        </div>
                        
                        <div style="margin-bottom: 25px;">
                            <h5><i class="fas fa-sitemap" style="color: #2196f3;"></i> Intent Organization</h5>
                            <ul style="padding-left: 20px; color: #555;">
                                <li><strong>Group related topics:</strong> Create intents for specific categories</li>
                                <li><strong>Avoid overlap:</strong> Ensure intents are distinct from each other</li>
                                <li><strong>Use contexts:</strong> Connect related intents for better flow</li>
                                <li><strong>Set priorities:</strong> Important intents should have higher priority</li>
                                <li><strong>Regular review:</strong> Update intents based on user interactions</li>
                            </ul>
                        </div>
                        
                        <div style="background: #f8fdf9; padding: 20px; border-radius: 8px; border: 1px solid #e0efe5; margin-top: 20px;">
                            <h5 style="color: var(--primary); margin-bottom: 10px;">Quick Examples</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="font-weight: 500; color: var(--primary); margin-bottom: 5px;">Good:</div>
                                    <div style="font-size: 0.9rem; color: #555;">
                                        • "How do I apply for annual leave?"<br>
                                        • "What's the process for sick leave?"<br>
                                        • "Can I take emergency leave?"
                                    </div>
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--danger); margin-bottom: 5px;">Avoid:</div>
                                    <div style="font-size: 0.9rem; color: #555;">
                                        • "Leave" (too vague)<br>
                                        • "Tell me about leaves" (unclear)<br>
                                        • "I want leave" (incomplete)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8f5e8;">
                        <button onclick="closeModal('trainingGuideModal')" class="btn-primary" style="padding: 12px 24px;">
                            Got it!
                        </button>
                        <button onclick="window.open('https://cloud.google.com/dialogflow/docs', '_blank')" class="btn-secondary" style="padding: 12px 24px;">
                            <i class="fas fa-external-link-alt"></i> Official Documentation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer.firstChild);
    
    setTimeout(() => {
        document.getElementById('trainingGuideModal').classList.add('active');
    }, 10);
}

// Close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 300);
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#ff9800'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 3000;
        animation: slideIn 0.3s ease;
        max-width: 400px;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// =============================================
// CONTENT MANAGEMENT TABS
// =============================================

// Initialize content management tabs
function initContentTabs() {
    const tabButtons = document.querySelectorAll('#content .dashboard-tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active tab button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show target tab content
            document.querySelectorAll('#content .dashboard-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(targetTab).classList.add('active');
            
            // Initialize specific tab if needed
            if (targetTab === 'dialogflow') {
                initDialogflow();
            }
        });
    });
    
    // Initialize Dialogflow if it's active
    const activeTab = document.querySelector('#content .dashboard-tab-btn.active');
    if (activeTab && activeTab.getAttribute('data-tab') === 'dialogflow') {
        initDialogflow();
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initContentTabs();
});

// Add to your existing JavaScript

// Search intents with API
async function searchIntentsApi(searchTerm) {
    try {
        const response = await fetch(`/admin/dialogflow/intents/api/search?search=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            filteredIntents = data.intents;
            currentPage = 1;
            renderIntents();
            return true;
        } else {
            showNotification('Search failed: ' + data.message, 'error');
            return false;
        }
    } catch (error) {
        console.error('Search error:', error);
        // Fall back to local search
        searchIntents(searchTerm);
        return false;
    }
}

// Get intent statistics
async function updateIntentStats() {
    try {
        const response = await fetch('/admin/dialogflow/intents/api/stats', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const stats = data.stats;
            
            // Update stats cards
            document.getElementById('totalIntents').textContent = stats.total_intents;
            document.getElementById('totalPhrases').textContent = stats.total_training_phrases;
            document.getElementById('totalResponses').textContent = stats.total_responses;
            
            // Update additional info if needed
            const updateStatus = document.getElementById('updateStatus');
            if (updateStatus) {
                updateStatus.textContent = `${stats.webhook_enabled} webhook enabled`;
            }
            
            return stats;
        }
    } catch (error) {
        console.error('Failed to load stats:', error);
        // Fall back to local calculation
        updateStats();
    }
}

// Bulk delete intents
async function bulkDeleteIntents(intentIds) {
    if (!intentIds.length) {
        showNotification('No intents selected for deletion', 'error');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${intentIds.length} intent(s)? This action cannot be undone.`)) {
        return;
    }
    
    try {
        const response = await fetch('/admin/dialogflow/intents/api/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ intent_ids: intentIds })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(`Successfully deleted ${data.deleted} intent(s)`, 'success');
            
            // Show errors if any
            if (data.errors && data.errors.length > 0) {
                const errorList = data.errors.map(e => `• ${e.intent_id}: ${e.error}`).join('\n');
                alert(`Some intents failed to delete:\n\n${errorList}`);
            }
            
            // Refresh the list
            loadIntents();
        } else {
            showNotification('Failed to delete intents: ' + data.message, 'error');
        }
    } catch (error) {
        showNotification('Error deleting intents: ' + error.message, 'error');
    }
}

// Update the search event listener to use API
document.getElementById('searchIntents')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value;
    
    // Clear previous timeout
    if (searchIntents.timeout) {
        clearTimeout(searchIntents.timeout);
    }
    
    // Set new timeout for API call
    searchIntents.timeout = setTimeout(async () => {
        if (searchTerm.length >= 2) {
            // Use API search for more accurate results
            await searchIntentsApi(searchTerm);
        } else if (searchTerm.length === 0) {
            // Reset to all intents
            filteredIntents = [...allIntents];
            currentPage = 1;
            renderIntents();
        }
    }, 300);
});

// Update loadIntents function to also get stats
async function loadIntents() {
    try {
        const syncBtn = document.getElementById('syncButton');
        if (syncBtn) syncBtn.disabled = true;
        
        // Load intents
        const response = await fetch('/admin/dialogflow/intents/api/list', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            allIntents = data.intents;
            filteredIntents = [...allIntents];
            renderIntents();
            
            // Load stats separately
            await updateIntentStats();
            
            // Hide loading row
            const loadingRow = document.getElementById('loadingRow');
            if (loadingRow) loadingRow.style.display = 'none';
            
            showNotification('Intents loaded successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to load intents');
        }
        
    } catch (error) {
        console.error('Error loading intents:', error);
        showNotification('Error loading intents: ' + error.message, 'error');
        
        // Show error in table
        const tbody = document.getElementById('intentsTableBody');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 24px; color: var(--warning);"></i>
                        <p style="margin-top: 10px;">Failed to load intents. Please check your Dialogflow configuration.</p>
                        <p style="font-size: 0.9rem; margin-top: 5px;">Error: ${error.message}</p>
                        <button class="btn-primary" onclick="loadIntents()" style="margin-top: 10px;">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </td>
                </tr>
            `;
        }
    } finally {
        const syncBtn = document.getElementById('syncButton');
        if (syncBtn) syncBtn.disabled = false;
    }
}
    </script>
</body>
</html>