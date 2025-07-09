
<style type="text/css">
	
table, td, th {
    border: 1px solid black;
}

	td { padding: 2px 5px 2px 5px; }

    table { border-collapse: collapse; }
</style>

<?php
	$mode=isset($_GET['mode']) ? $_GET['mode'] : "";
	switch ($mode) {
		default:
            $mode_note="";$mode_note2="";break;
	}
?>

<div style="background-color:#EEE;;width:100%;padding:20px 0 20px 0;font-family: Arial;color:#333;">
    <center>
        <div style="width:630px;background-color:#2b2985;padding:5px;border-radius:10px 10px 0 0;text-align: left;">    	</div>
        <div style="width:600px;background-color:#FFF;padding:20px;text-align: left;min-height:300px;padding-bottom: 40px;">
        	
        	<div style="height:80px;">
	        	<div style="float: left;padding-top:5px;width:400px;">
	        		<b style="font-size:28px;margin-top: 20px;">Notifikasi OTP</b><br/>
	        		<b style="font-size:14px;"><?php echo $mode_note; ?></b>
	        	</div>
	        	<div style="float: left;height:width:200px;">
	        		<img src="https://edwarmedika.com/Edwar_Healthcare.jpg" width="200px;">
	        	</div>

        	</div>
        	<div style="width:100%;height: 1px;background-color: #999;"></div>

        	<div style="width: 100%;">


                <div style="padding: 30px 0 20px 0;">
                    <b style="color:#2b2985;">Yth. Bapak/Ibu <?php echo ucwords($user['susrProfil']); ?>,</b><br/>
                    berikut ini adalah rincian dari kode OTP untuk login ke Sistem 2.1 Edwar Medika.
                </div>


                
                <table width="100%" style="margin-bottom: 20px;">
                    <tr>
                        <td width=""><b>Kode OTP</b></td>
                        <td><?php echo $user['otp']; ?></td>
                    </tr>
                    <tr>
                        <td width="145px"><b>Username</b></td>
                        <td><?php echo $user['susrNama']; ?></td>
                    </tr>
                    <tr>
                        <td width=""><b>Expired</b></td>
                        <td><?php echo date('d M Y H:i:s', strtotime($user['otp_date'])); ?></td>
                    </tr>
                </table>
                

                <?php if(false){ ?>

        		<div style="padding: 30px 0 20px 0;">
        			<b style="color:#2b2985;">Yth. Bapak/Ibu<?php echo isset($_GET['name']) ? (" ".$_GET['name']) : ""; ?>,</b><br/>
        			<?php echo $mode_note2; ?><br/>
        			berikut ini adalah rincian dari dokumen tersebut.
        		</div>
        		
        		<table width="100%" style="margin-bottom: 20px;">
        			<tr>
        				<td width="145px"><b>Nama Dokumen</b></td>
        				<td><?php echo $doc['doc_name']; ?></td>
        			</tr>
        			<tr>
        				<td width=""><b>Inisiator</b></td>
        				<td><?php echo $usr['name']; ?></td>
        			</tr>
                    <tr>
                        <td width=""><b>Tanggal Dibuat</b></td>
                        <td><?php echo date('d M Y H.i', strtotime($doc['added'])); ?></td>
                    </tr>
        		</table>
                <div>
                	<a href="" target="_blank" 
                	style="margin-bottom:30px;padding: 10px 20px 10px 20px;background-color:#2b2985;text-decoration: none;border-radius: 10px;color: #FFF;">
	                	<b>Buka Dokumen</b>
	                </a>
                </div>
                <?php } ?>

                

        	</div>
    	</div>
        <div style="width:630px;background-color:#818181;padding:5px;border-radius:0 0 10px 10px;"></div>
    </center>
</div>