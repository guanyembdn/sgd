<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(!isset($_GET["mes"]) || !is_numeric($_GET["mes"])) die();

$sumatotal = 0;
$numpagaments = 0;

$sth = $con->query("SELECT * FROM infoentitat WHERE id=1");
$rowentitat = $sth->fetchAll(PDO::FETCH_ASSOC);

$sth = $con->prepare("SELECT p.nomusuari, p.Nom_i_Cognoms, p.IBAN, p.BIC, q.quota AS quotafinal FROM quotes AS q INNER JOIN persones AS p ON q.uid = p.id WHERE ((MONTH(q.startdate)-(:mes)) % p.Periodicitat_Quota = 0)");
$sth->bindParam(":mes", intval($_GET["mes"]), PDO::PARAM_INT);
$sth->execute();
$rquotes = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($rquotes as $row){
	$sumatotal += $row["quotafinal"];
	$numpagaments++;
}

header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="sepa-Descarregat-' . date('d-m-Y') . '-Mes'.intval($_GET["mes"]).'.xml"');

echo '        <Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02" 
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                  xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02 pain.008.001.02.xsd">
          <CstmrDrctDbtInitn>
            <GrpHdr>
              <MsgId>MSG' . date('d-m-Y') . '</MsgId>
              <CreDtTm>' . date('Y-m-d\TH:i') . '</CreDtTm>
              <NbOfTxs>' . $numpagaments . '</NbOfTxs>
              <CtrlSum>' . $sumatotal . '</CtrlSum>
              <InitgPty>
                <Nm>' . safe_escape($rowentitat[0]["Nom"]) . '</Nm>
              </InitgPty>
            </GrpHdr>
            <PmtInf>
              <PmtInfId>1</PmtInfId>
              <PmtMtd>DD</PmtMtd>
              <BtchBookg>false</BtchBookg>
              <NbOfTxs>' . $numpagaments . '</NbOfTxs>
              <CtrlSum>' . $sumatotal .'</CtrlSum>
              <PmtTpInf>
                <SvcLvl>
                  <Cd>SEPA</Cd>
                </SvcLvl>
                <LclInstrm>
                  <Cd>CORE</Cd>
                </LclInstrm>
                <SeqTp>FRST</SeqTp>
              </PmtTpInf>
              <ReqdColltnDt>' . date('Y-m-d') . '</ReqdColltnDt>
              <Cdtr>
                <Nm>' . safe_escape($rowentitat[0]["Nom"]) . '</Nm>
              </Cdtr>
              <CdtrAcct>
                <Id>
                  <IBAN>' . safe_escape($rowentitat[0]["IBAN"]) . '</IBAN>
                </Id>
              </CdtrAcct>
              <CdtrAgt>
                <FinInstnId>
                  <BIC>' . safe_escape($rowentitat[0]["BIC"]) . '</BIC>
                </FinInstnId>
              </CdtrAgt>
              <ChrgBr>SLEV</ChrgBr>
              <CdtrSchmeId>
                <Id>
                  <PrvtId>
                    <Othr>
                      <Id>' . safe_escape($rowentitat[0]["PrvtId"]) . '</Id>
                      <SchmeNm>
                        <Prtry>SEPA</Prtry>
                      </SchmeNm>
                    </Othr>
                  </PrvtId>
                </Id>
              </CdtrSchmeId>';
      
$i = 1;
foreach($rquotes as $row){
	echo '              <DrctDbtTxInf>
                <PmtId>
                  <InstrId>' . $i . '</InstrId>
                  <EndToEndId>' . $i . '</EndToEndId>
                </PmtId>
                <InstdAmt Ccy="EUR">' . $row["quotafinal"] . '</InstdAmt>
                <DrctDbtTx>
                  <MndtRltdInf>
                    <MndtId>' . $i. '</MndtId>
                    <DtOfSgntr>' . date('Y-m-d') . '</DtOfSgntr>
                  </MndtRltdInf>
                </DrctDbtTx>
                <DbtrAgt>
                  <FinInstnId>
                    <BIC>' . safe_escape($row["BIC"]) . '</BIC>
                  </FinInstnId>
                </DbtrAgt>
                <Dbtr>
                  <Nm>' . safe_escape($row["Nom_i_Cognoms"]) . '</Nm>
                </Dbtr>
                <DbtrAcct>
                  <Id>
                    <IBAN>' . safe_escape($row['IBAN']) . '</IBAN>
                  </Id>
                </DbtrAcct>
                <RmtInf>
                  <Ustrd>Pago ' . $i. '</Ustrd>
                </RmtInf>
              </DrctDbtTxInf>';
	$i++;
}
	  
echo '            </PmtInf>
          </CstmrDrctDbtInitn>
        </Document>';
?>