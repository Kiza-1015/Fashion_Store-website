<?php
$page_title = 'Customers - LuxeMode Admin';
require_once '../includes/header.php';
require_admin();

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    if ($did !== $_SESSION['customer_id']) $conn->query("DELETE FROM customers WHERE customer_id=$did AND role='customer'");
    header('Location: customers.php?deleted=1'); exit;
}

$customers = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM orders WHERE customer_id=c.customer_id) AS orders FROM customers c ORDER BY c.created_at DESC");
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header-bar"><h2>Customers</h2></div>
        <?php if (!empty($_GET['deleted'])): ?><div class="alert alert-success">Customer deleted.</div><?php endif; ?>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Orders</th><th>Joined</th><th></th></tr></thead>
            <tbody>
                <?php while ($c = $customers->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['customer_id'] ?></td>
                    <td><strong><?= clean($c['fullname']) ?></strong></td>
                    <td><?= clean($c['email']) ?></td>
                    <td><?= clean($c['phone'] ?? '—') ?></td>
                    <td><span style="font-size:.72rem;font-weight:700;padding:2px 10px;background:<?= $c['role']==='admin'?'#dbeafe':'#f3f4f6' ?>;color:<?= $c['role']==='admin'?'#1e40af':'#374151' ?>;"><?= ucfirst($c['role']) ?></span></td>
                    <td><?= $c['orders'] ?></td>
                    <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    <td><?php if ($c['role']!=='admin'): ?><a href="?delete=<?= $c['customer_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a><?php endif; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
