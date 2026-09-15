<?php
declare(strict_types=1);
final class NumberingService {
    public static function assign(PDO $db,int $documentId): string {
        $q=$db->prepare('SELECT d.document_date,d.institution_id,d.owner_unit_id,d.numbering_scheme_id,ns.*,ou.code unit_code,i.code institution_code FROM documents d JOIN numbering_schemes ns ON ns.id=d.numbering_scheme_id JOIN organization_units ou ON ou.id=d.owner_unit_id JOIN institutions i ON i.id=d.institution_id WHERE d.id=? FOR UPDATE');$q->execute([$documentId]);$r=$q->fetch();if(!$r)throw new RuntimeException('Skema penomoran belum dipilih.',422);
        $date=new DateTimeImmutable($r['document_date']?:'now');$period=$r['reset_period']==='monthly'?$date->format('Y-m'):($r['reset_period']==='yearly'?$date->format('Y'):'all');
        $db->prepare('INSERT INTO numbering_sequences(scheme_id,period_key,last_number) VALUES(?,?,0) ON DUPLICATE KEY UPDATE last_number=last_number')->execute([$r['numbering_scheme_id'],$period]);
        $q=$db->prepare('SELECT id,last_number FROM numbering_sequences WHERE scheme_id=? AND period_key=? FOR UPDATE');$q->execute([$r['numbering_scheme_id'],$period]);$seq=$q->fetch();$next=(int)$seq['last_number']+1;$db->prepare('UPDATE numbering_sequences SET last_number=?,lock_version=lock_version+1 WHERE id=?')->execute([$next,$seq['id']]);
        $roman=['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];$number=strtr($r['format_pattern'],['{SEQUENCE}'=>str_pad((string)$next,(int)$r['padding_length'],'0',STR_PAD_LEFT),'{UNIT}'=>$r['unit_code'],'{INSTITUTION}'=>$r['institution_code'],'{ROMAN_MONTH}'=>$roman[(int)$date->format('n')-1],'{MONTH}'=>$date->format('m'),'{YEAR}'=>$date->format('Y')]);
        $db->prepare('UPDATE documents SET number=? WHERE id=?')->execute([$number,$documentId]);return $number;
    }
}
