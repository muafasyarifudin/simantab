<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/bootstrap.php';
header('Content-Security-Policy: default-src \'none\'; frame-ancestors \'none\'');
header('Referrer-Policy: no-referrer');
$method=$_SERVER['REQUEST_METHOD'];$route=trim($_GET['route']??($_SERVER['PATH_INFO']??''),'/');
try {
    if($route==='auth/login'&&$method==='POST'){$in=Security::jsonInput();if(!AuthService::attempt(trim($in['identity']??''),(string)($in['password']??'')))Response::error('Identitas atau kata sandi salah.',401);Response::json(['user'=>AuthService::user(),'csrf_token'=>Security::csrfToken()]);}
    if($route==='auth/logout'&&$method==='POST'){Security::verifyCsrf($_SERVER['HTTP_X_CSRF_TOKEN']??null);AuthService::logout();Response::json(['message'=>'Berhasil keluar.']);}
    AuthService::requireUser();
    if($route==='auth/me'&&$method==='GET')Response::json(['user'=>AuthService::user(),'csrf_token'=>Security::csrfToken()]);
    if($method!=='GET')Security::verifyCsrf($_SERVER['HTTP_X_CSRF_TOKEN']??($_POST['_token']??null));
    if($route==='notifications/read-all'&&$method==='POST'){ $q=Database::connection()->prepare('UPDATE notifications SET read_at=NOW() WHERE user_id=? AND read_at IS NULL');$q->execute([AuthService::requireUser()['id']]);Response::json(['updated'=>$q->rowCount()]); }
    if($route==='documents'&&$method==='GET'){RbacService::authorize('documents.view');Response::json(DocumentService::list($_GET));}
    if($route==='documents'&&$method==='POST')Response::json(DocumentService::create(Security::jsonInput()),201);
    if(preg_match('#^documents/(\d+)$#',$route,$m)){$id=(int)$m[1];if($method==='GET'){RbacService::authorize('documents.view');Response::json(DocumentService::find($id));}if(in_array($method,['PATCH','PUT'],true))Response::json(DocumentService::update($id,Security::jsonInput()));if($method==='DELETE'){DocumentService::delete($id);Response::json(['message'=>'Draf dihapus.']);}}
    if(preg_match('#^documents/(\d+)/submit$#',$route,$m)&&$method==='POST')Response::json(WorkflowService::submit((int)$m[1]));
    if(preg_match('#^documents/(\d+)/files$#',$route,$m)&&$method==='POST')Response::json(FileService::uploadDocument((int)$m[1],$_FILES['file']??[],$_POST['role']??'attachment'),201);
    if(preg_match('#^documents/(\d+)/pdf$#',$route,$m)&&$method==='POST')Response::json(PdfService::generateFinal((int)$m[1]),201);
    if(preg_match('#^documents/(\d+)/distribute$#',$route,$m)&&$method==='POST'){$in=Security::jsonInput();Response::json(['distribution_id'=>OperationsService::distribute((int)$m[1],$in['recipients']??[])],201);}
    if(preg_match('#^documents/(\d+)/dispositions$#',$route,$m)&&$method==='POST')Response::json(['id'=>OperationsService::disposition((int)$m[1],Security::jsonInput())],201);
    if(preg_match('#^documents/(\d+)/reports$#',$route,$m)&&$method==='POST')Response::json(['id'=>OperationsService::report((int)$m[1],Security::jsonInput())],201);
    if(preg_match('#^approvals/(\d+)/(approve|revise|reject)$#',$route,$m)&&$method==='POST'){$map=['approve'=>'approved','revise'=>'revision','reject'=>'rejected'];$in=Security::jsonInput();WorkflowService::decide((int)$m[1],$map[$m[2]],$in['notes']??null);Response::json(['message'=>'Keputusan tersimpan.']);}
    if(preg_match('#^files/(\d+)/download$#',$route,$m)&&$method==='GET')FileService::download((int)$m[1]);
    if($route==='notifications'&&$method==='GET'){$u=AuthService::requireUser();$q=Database::connection()->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 100');$q->execute([$u['id']]);Response::json($q->fetchAll());}
    if($route==='agendas'&&$method==='GET')Response::json(AgendaService::listing());
    if($route==='agendas'&&$method==='POST')Response::json(AgendaService::save(null,Security::jsonInput()),201);
    if(preg_match('#^agendas/(\d+)$#',$route,$m)&&$method==='PATCH')Response::json(AgendaService::save((int)$m[1],Security::jsonInput()));
    Response::error('Endpoint tidak ditemukan.',404);
} catch(Throwable $e){$code=(int)$e->getCode();if($code<400||$code>599)$code=$e instanceof InvalidArgumentException?422:500;if($code===500)error_log($e);Response::error($code===500?'Terjadi kesalahan internal.':$e->getMessage(),$code);}
