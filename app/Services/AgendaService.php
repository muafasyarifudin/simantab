<?php
declare(strict_types=1);
final class AgendaService {
 public static function listing(): array {
  RbacService::authorize('agendas.view');
  $q=Database::connection()->prepare('SELECT * FROM agendas WHERE owner_user_id=? ORDER BY starts_at,id');$q->execute([AuthService::requireUser()['id']]);return $q->fetchAll();
 }
 public static function save(?int $id,array $input): array {
  RbacService::authorize('agendas.view');$user=AuthService::requireUser();
  return Database::transaction(function(PDO $db)use($id,$input,$user){
   $old=null;if($id){$q=$db->prepare('SELECT * FROM agendas WHERE id=? AND owner_user_id=? FOR UPDATE');$q->execute([$id,$user['id']]);$old=$q->fetch();if(!$old)throw new RuntimeException('Agenda tidak ditemukan.',404);if($old['source']!=='manual')throw new RuntimeException('Agenda dari surat hanya dapat diubah melalui surat asal.',409);}
   $title=trim((string)($input['title']??''));if($title===''||mb_strlen($title)>255)throw new InvalidArgumentException('Judul wajib diisi, maksimum 255 karakter.');
   $dates=[];foreach(['starts_at','ends_at'] as $key){$v=str_replace('T',' ',(string)($input[$key]??''));$d=DateTimeImmutable::createFromFormat('!Y-m-d H:i',$v,new DateTimeZone('Asia/Jakarta'));if(!$d||$d->format('Y-m-d H:i')!==$v)throw new InvalidArgumentException('Tanggal dan jam wajib valid.');$dates[$key]=$d->format('Y-m-d H:i:s');}
   if($dates['ends_at']<=$dates['starts_at'])throw new InvalidArgumentException('Waktu selesai harus setelah waktu mulai.');
   $status=$input['status']??'scheduled';if(!in_array($status,['scheduled','completed','cancelled'],true))throw new InvalidArgumentException('Status tidak valid.');
   $location=trim((string)($input['location']??''));$description=trim((string)($input['description']??''));if(mb_strlen($location)>255||mb_strlen($description)>10000)throw new InvalidArgumentException('Lokasi atau deskripsi terlalu panjang.');
   $values=[$title,$description,$dates['starts_at'],$dates['ends_at'],$location,$status];
   if($id){$db->prepare('UPDATE agendas SET title=?,description=?,starts_at=?,ends_at=?,location=?,status=? WHERE id=? AND owner_user_id=?')->execute([...$values,$id,$user['id']]);}
   else{$db->prepare("INSERT INTO agendas(title,description,starts_at,ends_at,location,status,owner_user_id,source,timezone) VALUES(?,?,?,?,?,?,?,'manual','Asia/Jakarta')")->execute([...$values,$user['id']]);$id=(int)$db->lastInsertId();}
   AuditService::log($old?'agenda.updated':'agenda.created','agenda',$id,null,['status'=>$status]);
   $q=$db->prepare('SELECT * FROM agendas WHERE id=?');$q->execute([$id]);return $q->fetch();
  });
 }
}
