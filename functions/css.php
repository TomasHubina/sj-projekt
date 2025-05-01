<?php
// -----------------------------------
// STYLY PRE ADMIN/INDEX.PHP 
// -----------------------------------
function admin_css() {
    echo '<style>
    .admin-sidebar {
            min-height: calc(100vh - 56px);
            background-color: rgba(0, 0, 0, 0.75);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        
        .admin-sidebar .nav-link:hover, 
        .admin-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .admin-card {
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-header {
            background-color: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table {
            color: #fff;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .table td, .table th {
            border-color: rgba(255, 255, 255, 0.1);
        }
        </style>';
}
?>