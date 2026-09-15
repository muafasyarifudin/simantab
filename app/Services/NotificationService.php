<?php
declare(strict_types=1);
final class NotificationService {
    public static function send(int $userId,string $key,string $type,string $title,string $message,?string $objectType=null,?int $objectId=null,?string $link=null): void {
        $db=Database::connection();$q=$db->prepare('INSERT IGNORE INTO notifications(user_id,event_key,notification_type,title,message,object_type,object_id,deep_link) VALUES(?,?,?,?,?,?,?,?)');$q->execute([$userId,$key,$type,$title,$message,$objectType,$objectId,$link]);if($q->rowCount()){ $id=(int)$db->lastInsertId();$db->prepare("INSERT IGNORE INTO notification_deliveries(notification_id,channel,status) VALUES(?,'in_app','sent'),(?,'email','queued')")->execute([$id,$id]); }
    }
}
