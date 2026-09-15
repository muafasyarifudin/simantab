<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=Database::connection();$now=date('Y-m-d H:i:s');
$q=$db->prepare("SELECT r.*,u.email,u.id user_id FROM reminders r JOIN users u ON u.id=r.user_id WHERE r.status='scheduled' AND r.remind_at<=? ORDER BY r.remind_at LIMIT 50");$q->execute([$now]);
foreach($q as $r){try{NotificationService::send((int)$r['user_id'],'reminder-'.$r['id'],'reminder','Pengingat agenda atau laporan','Ada agenda atau laporan yang memerlukan perhatian Anda.');$db->prepare("UPDATE reminders SET status='sent',sent_at=NOW(),attempts=attempts+1 WHERE id=?")->execute([$r['id']]);}catch(Throwable $e){$db->prepare("UPDATE reminders SET status='failed',attempts=attempts+1,last_error=? WHERE id=?")->execute([$e->getMessage(),$r['id']]);}}
$q=$db->query("SELECT nd.*,n.title,n.message,u.email FROM notification_deliveries nd JOIN notifications n ON n.id=nd.notification_id JOIN users u ON u.id=n.user_id WHERE nd.channel='email' AND nd.status='queued' ORDER BY nd.id LIMIT 50");
foreach($q as $d){$ok=$d['email']?@mail($d['email'],$d['title'],$d['message'],"Content-Type: text/plain; charset=UTF-8\r\n"):false;$db->prepare('UPDATE notification_deliveries SET status=?,attempts=attempts+1,sent_at=IF(?,NOW(),sent_at),last_error=? WHERE id=?')->execute([$ok?'sent':'failed',$ok?1:0,$ok?null:'mail transport rejected',$d['id']]);}
echo "Worker completed at {$now}\n";
