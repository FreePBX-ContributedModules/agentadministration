</div>

<br>

<!-- The agent dropdown part -->
<div class="rnav"><ul>

<?

	//Basic variable initialization and setup

	$LargestAgentID=agentadministration_getlargestagentid(); //Get the largest Agent ID
	$data=explode(",",agentadministration_getagentinfo($_GET["ai"],$LargestAgentID)); //Get all agent information and store in this array

	$title=$data[0];
	$NextAgentID=$data[1];
	$agentname=$data[2];
	$agentID=$data[3];
	$agentpassword=$data[4];

?>
<?
								
	$agentinfo=agentadministration_getdropdownlist(); //Get the huge string that will contain the dropdown list information
                                                             
     copy("/etc/asterisk/agents.conf","/etc/asterisk/agents.conf.bak"); //Make backup of original agents.conf file
                                        
     echo "<td valign=top>";
                                        
     echo "<b>Current List:</b><br>";
                                        
    $dropdownname=explode(",",$agentinfo); //Breakdown string into array
                                                                
    sort($dropdownname,SORT_STRING); //Sorts the array
    
	echo "<li><a href='config.php?display=agentadministration&nai=".$NextAgentID."'>Add Agent</a></li>";

	for ($ct=0;$ct<=count($dropdownname)-1;$ct++) {
			
        $passwordstartpos=strrpos($dropdownname[$ct],"::"); //Chop off password
	
		echo "<li><a href='config.php?display=agentadministration&ai=displayname&ai=".$dropdownname[$ct]."&nai=".$NextAgentID."'>".substr($dropdownname[$ct],0,$passwordstartpos-1)."</a></li>";
		
	}
	
?>
</ul></div>

<div class="content">

<table align=left valign=top border=0>
	<tr><td align=left colspan=3>
		<table border=0>
		
		<?
			
			//Gets full file path as well as query strings
			$urladdress=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']
		
		?>
                                                                                   
		<form name=frmAgentInformation method="post" action="<? echo $urladdress; ?>">
		                                                                                                              
		<tr>
			<td colspan=3><font size=4><b><? echo $title; ?></b></font>
            </td>
        </tr>
                                                                                                
		<tr><td colspan=3 height=20></td></tr>              
                                                                                                        
        <tr>
			<td colspan=3> <!-- Hidden values -->
                <input type=hidden name=h_agentname value='<? echo $agentname; ?>'>
				<input type=hidden name=h_agentid value='<? echo $NextAgentID; ?>'>
                <input type=hidden name=h_agentpassword value='<? echo $agentpassword; ?>'>
			</td>
        </tr>
                                                                                                                                                        
        <tr>
			<td>Agent Name:</td>
            <td width=10></td>
             <td><input type=textbox name=tb_agentname value='<? echo $agentname; ?>'></td>
        </tr>
                                                                                                                
        <tr>
             <td>Agent ID:</td>
             <td width=10></td>
             <td><input type=textbox name=tb_agentid value='<? echo $NextAgentID; ?>' onkeypress="return fnOnlyNumbers(event);"></td>
        </tr>
                                                                                                
         <tr>
            <td>Agent Password:</td>
            <td width=10></td>
            <td><input type=textbox name=tb_password value='<? echo $agentpassword; ?>' onkeypress="return fnOnlyNumbers(event);"></td>
        </tr>
                                                                                                
        <tr><td colspan=3 height=20></td></tr>
                                                                                                
        <tr><td colspan=3><input type=submit name=b_submit value='Submit Changes' onClick="return fnInputChecks();"></td></tr>
                                                                                                                       
</table></td></tr></table>

<!-- Used to push down footer -->
<br><br><br><br><br><br><br><br><br><br><br><br>

<?
         
    if (isset($_POST['b_submit'])){ //The information to submit
       
        $originalentry="agent => ".$_POST['h_agentid'].",".$_POST['h_agentpassword'].",".trim($_POST['h_agentname']); //The original entry based on hiddem values

        $newentry="agent => ".$_POST['tb_agentid'].",".$_POST['tb_password'].",".trim($_POST['tb_agentname']);
        
        agentadministration_searchreplacewithinfile("agents.conf",$originalentry,$newentry); //Adds new entries as well as serach and replaces within the "agents.conf" file.
 		
		needreload(); //Show the "Appply Configuration Changes" button
		redirect_standard(); //Redirect to same page
                                
    }
                        
?>

<script language="javascript">

function fnInputChecks() {

     var agentname=document.frmAgentInformation.tb_agentname.value; //For agent name
     var agentid=document.frmAgentInformation.tb_agentid.value; //For agent id
     var agentpassword=document.frmAgentInformation.tb_password.value; //For agent password
        
     //Makes sure an agent name is entered
     if (agentname=="") {
        
		alert ("Please enter an Agent name.");
        document.frmAgentInformation.tb_agentname.focus();
        
        return false;
        
    }
        
    //Makes sure an agent id is entered
    if (agentid=="") {
        
        alert ("Please enter an Agent ID.");
        document.frmAgentInformation.tb_agentid.focus();
                
        return false;
                
    }

    //Makes sure a password is entered
    if (agentpassword=="") {
        
        alert ("Please enter an Agent password.");
        document.frmAgentInformation.tb_password.focus();
                
        return false;
                
    }       
                
    //Makes sure agent id and password are not the same
    if (agentid==agentpassword) {
        
        alert ("Agent ID and password cannot be the same.");
                
        document.frmAgentInformation.tb_password.value="";
        document.frmAgentInformation.tb_password.focus();

        return false;
                        
    }
        
        //Makes sure password is 4 characters or more
    if (agentpassword.length<=3) {
        
        alert ("Agent password must be at least 4 characters or longer.");
                
        document.frmAgentInformation.tb_password.focus();

        return false;
                
    }

    return true;
        
}

function fnOnlyNumbers(evt) {

    //Allow only Numbers/Digits in TextBox

    var charCode = (evt.which) ? evt.which : event.keyCode
    
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        
        alert ("Please enter only numbers.");   
        return false;

    }
        
    return true;

}

</script>
                                

