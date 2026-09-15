<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/model/config/config.app.php';
require __DIR__ . '/model/function/function.main.php';

$route = trim((string)($_GET['page'] ?? 'dashboard'));
$publicRoutes = ['login', 'verify'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $route === 'login') {
    try {
        Security::verifyCsrf($_POST['_token'] ?? null);
        if (AuthService::attempt(trim((string)($_POST['username'] ?? '')), (string)($_POST['password'] ?? ''))) {
            header('Location: index.php?page=dashboard'); exit;
        }
        $loginError = 'Username/email/NIP atau kata sandi salah.';
    } catch (Throwable $e) { $loginError = $e->getMessage(); }
}
if ($route === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=dashboard'); exit; }
    Security::verifyCsrf($_POST['_token'] ?? null);
    AuthService::logout();
    header('Location: index.php?page=login');
    exit;
}

if ($route === 'login') {
    require __DIR__ . '/view/private/page/page.login.php';
    exit;
}
if ($route === 'verify') {
    require __DIR__ . '/view/public/pages/verify.php';
    exit;
}
if (!AuthService::user()) { header('Location: index.php?page=login'); exit; }
$_SESSION['simantap_user'] = AuthService::user();
$documents = array_map(static fn(array $d): array => [
    'id'=>(int)$d['id'],'number'=>$d['number'] ?: 'DRAF-'.str_pad((string)$d['id'],5,'0',STR_PAD_LEFT),
    'title'=>$d['title'],'type'=>$d['type_name'],'unit'=>$d['unit_name'] ?: '-',
    'date'=>$d['document_date'] ?: substr($d['created_at'],0,10),
    'status'=>match($d['status']){'draft'=>'Draft','under_review'=>'Dalam Pemeriksaan','revision'=>'Perlu Revisi','approved'=>'Disetujui','signed'=>'Ditandatangani','distributed'=>'Didistribusikan','completed'=>'Selesai',default=>ucwords(str_replace('_',' ',$d['status']))},
    'priority'=>ucfirst($d['priority']),'assignee'=>'-'
], DocumentService::list());
$approvals=[];
$q=Database::connection()->prepare('SELECT ws.id,d.title,d.number,e.full_name requester,ou.short_name unit,d.submitted_at,ws.name stage,TIMESTAMPDIFF(MINUTE,ws.activated_at,NOW()) wait_minutes,d.priority FROM workflow_step_actors a JOIN workflow_steps ws ON ws.id=a.workflow_step_id JOIN workflow_instances wi ON wi.id=ws.workflow_instance_id JOIN documents d ON d.id=wi.document_id JOIN users u ON u.id=d.creator_id LEFT JOIN employees e ON e.id=u.employee_id JOIN organization_units ou ON ou.id=d.owner_unit_id WHERE a.user_id=? AND a.status="pending" AND ws.status="active"');
$q->execute([AuthService::user()['id']]);foreach($q as $a)$approvals[]=['id'=>$a['id'],'title'=>$a['title'],'number'=>$a['number']?:'Draf','requester'=>$a['requester']?:'Pengguna','unit'=>$a['unit'],'submitted'=>$a['submitted_at']?:'-','stage'=>$a['stage'],'sla'=>$a['wait_minutes'].' menit','priority'=>ucfirst($a['priority'])];
$assignments=[];$q=Database::connection()->prepare('SELECT d.title,d.number,d.effective_from,d.effective_until,a.status,COUNT(*) OVER(PARTITION BY d.id) team_count FROM document_assignees a JOIN documents d ON d.id=a.document_id JOIN employees e ON e.id=a.employee_id JOIN users u ON u.employee_id=e.id WHERE u.id=?');$q->execute([AuthService::user()['id']]);foreach($q as $a)$assignments[]=['title'=>$a['title'],'number'=>$a['number']?:'Draf','date'=>($a['effective_from']?:'-').' – '.($a['effective_until']?:'-'),'location'=>'Lihat dokumen','progress'=>$a['status']==='completed'?100:($a['status']==='in_progress'?50:10),'status'=>$a['status']==='completed'?'Selesai':'Dalam Pelaksanaan','team'=>$a['team_count']];
$notifications=[];$q=Database::connection()->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 20');$q->execute([AuthService::user()['id']]);foreach($q as $n)$notifications[]=['icon'=>'ri-notification-3-line','tone'=>$n['priority']==='urgent'?'rose':'blue','title'=>$n['title'],'text'=>$n['message'],'time'=>$n['created_at'],'unread'=>!$n['read_at']];

$allowedRoutes = ['dashboard', 'documents', 'create-document', 'document-detail', 'letter-editor', 'approvals', 'assignments', 'agenda', 'reports', 'archive', 'notifications', 'users', 'units', 'templates', 'numbering', 'audit', 'settings', 'changelog'];
if (!in_array($route, $allowedRoutes, true)) {
    http_response_code(404);
    $route = '404';
}

require __DIR__ . '/view/public/layouts/head.php';
require __DIR__ . '/view/public/layouts/sidebar.php';
?>
<main class="main-content">
    <?php require __DIR__ . '/view/public/layouts/topbar.php'; ?>
    <div class="page-content">
        <?php require __DIR__ . '/controller/route/route.main.php'; ?>
    </div>
    <?php require __DIR__ . '/view/public/layouts/footer.php'; ?>
</main>
<div class="sidebar-backdrop" data-sidebar-close></div>
<?php require __DIR__ . '/view/public/layouts/notification-drawer.php'; ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/interactions.js?v=<?= time() ?>"></script>
</body></html>
