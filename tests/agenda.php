<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$db=Database::connection();$u=$db->query("SELECT u.id,u.institution_id,ur.unit_id FROM users u JOIN user_roles ur ON ur.user_id=u.id WHERE u.username='admin' LIMIT 1")->fetch();
if(!$u)throw new RuntimeException('Admin fixture required');$_SESSION['auth_user']=$u;
try {
 $data=['title'=>'Agenda test','starts_at'=>'2026-09-20T08:00','ends_at'=>'2026-09-20T09:00','location'=>'Ruang uji','status'=>'scheduled'];
 $a=AgendaService::save(null,$data);$id=$a['id'];
 if($a['source']!=='manual'||$a['owner_user_id']!=$u['id'])throw new RuntimeException('Ownership failed');
 $data['title']='Agenda test updated';$a=AgendaService::save((int)$id,$data);if($a['title']!==$data['title'])throw new RuntimeException('Update failed');
 try{AgendaService::save((int)$id,array_merge($data,['ends_at'=>'2026-09-19T08:00']));throw new RuntimeException('Invalid date accepted');}catch(InvalidArgumentException){}
 $_SESSION['auth_user']['id']=0;try{AgendaService::save((int)$id,$data);throw new RuntimeException('Foreign owner accepted');}catch(RuntimeException $e){if(!in_array($e->getCode(),[403,404]))throw $e;}$_SESSION['auth_user']=$u;
 $data['status']='cancelled';$a=AgendaService::save((int)$id,$data);if($a['status']!=='cancelled')throw new RuntimeException('Cancel failed');
 echo "PASS: create, update, date validation, owner authorization, cancel\n";
}finally{$_SESSION['auth_user']=$u;if($db->inTransaction())$db->rollBack();if(isset($id)){$db->prepare('DELETE FROM audit_logs WHERE object_type=? AND object_id=?')->execute(['agenda',$id]);$db->prepare('DELETE FROM agendas WHERE id=? AND owner_user_id=?')->execute([$id,$u['id']]);}}
