<?

    Function agentadministration_reformatnameentry($nameentry,$returntype) {
	
		//This function returns a reformatted entry for the dropdown
                        
        $newstr=substr($nameentry,9); //Chop off the "agent =>" part.
                
        $data=explode(",",$newstr); //Put in array

        /*
                
			$data[0] -  The Agent ID
			$data[1] -  The Agent  password
			$data[2] -  The Agent  name
                
		*/
        
        if ($returntype==0)	{ //If returntype=0, return full entry ((Agent ID, Agent name and Agent password)
                                         
			$newnameentry=trim($data[0]).":".trim($data[2])."::".trim($data[1]);
            return $newnameentry;   
        
        }
                
			else return trim($data[0]); //If returntype=1, return just Agent ID.
                
    }
    
    
    Function agentadministration_searchreplacewithinfile($Filename,$Searchstring,$Replacestring) {
	
		//This function provides a serach and replace within the file. It uses a temp file to create the new file and then renames that.
                        
		$f_read=fopen("/etc/asterisk/".$Filename,"r"); //Read the original agents.conf file
        $f_write=fopen("/etc/asterisk/mytemp.conf","w+"); //For writing to the temp file
        $rewriteflag=0; //If we are rewriting. Set to off.
        
        while ($line=fgets($f_read,9000) ) {
                
			//Compare the searchstring with the text
            if (strcmp(trim($Searchstring),trim($line))==0) { //A match is found. Also case sensitive.
                        
                $rewriteflag=1; //Set rewriting to on.
                fwrite($f_write,trim($Replacestring)."\n"); //If both strings are equal, replace original entry with new entry.
                
            }
                
            else fwrite($f_write,trim($line)."\n"); //No strings match, just write original line.
                                                
        }
                
        if ($rewriteflag==0) fwrite($f_write,trim($Replacestring)."\n"); //Add new entries
                
        rename("/etc/asterisk/mytemp.conf","/etc/asterisk/".$Filename); //Renames temp file.
        
		//Close both files        
        fclose($f_read);
        fclose($f_write);
				
		return;
                
     }
	
	
	Function agentadministration_getdropdownlist() {

		// This function is used for the agent dropdown list. This will put all agents ids and name in one huge string 
        
		$f_agents=fopen("/etc/asterisk/agents.conf","r"); //Read the original agents.conf file
							
		$gridname_totalentries="";
			
		while ($line=fgets($f_agents,9000) ) {
											
			$substring=substr($line,0,8); //Find the "agent =>" part.
																	
			if ($substring=="agent =>") {
										
				//This part concatenates the individual agent entries into one huge string
				if ($gridname_totalentries=="") $gridname_totalentries=agentadministration_reformatnameentry($line,0); //For the very first line
							
				else $gridname_totalentries=$gridname_totalentries.",".agentadministration_reformatnameentry($line,0); //For every other line
							
			}			
					
		 }
		
		//Close file		
		fclose($f_agents);
		
		return $gridname_totalentries;
		
	}	

	
		Function agentadministration_getlargestagentid() {

			//This function will get the larget Agent ID contained in the dropdown list.
	
			$f_agents=fopen("/etc/asterisk/agents.conf","r"); //Read the original agents.conf file
			$LargestAgentID=0;
				
			while ($line=fgets($f_agents,9000) ) {
												
				$substring=substr($line,0,8); //Find the "agent =>" part.
																		
				if ($substring=="agent =>") {
						
					//This part just keeps the largest AgentID
					if (agentadministration_reformatnameentry($line,1)>$LargestAgentID && agentadministration_reformatnameentry($line,1)<2008) $LargestAgentID=agentadministration_reformatnameentry($line,1);
								
				}
									
			 }
			
			//Close file			
			fclose($f_agents);
			
			return $LargestAgentID;
		
		}
		
	
	Function agentadministration_getagentinfo($currentagentid,$largestagentid) {
	
		//This function returns the all information of the agent chosen from the dropdown to be used for the text boxes
		
		//An agent is chosen from the dropdown, just get the necessary information		
		if ($currentagentid>0) {   
			
			$agentinfo=str_replace("::",":",$currentagentid); //Replace :: with single :
					
			$data=explode(":",$agentinfo); //Put in array
					
			/*
					
				$data [0] - Agent ID
				$data [1] - Agent Name
				$data [2] - Agent Password
									
			*/
					
			$title="Agent: ".$data[1]." (".$data[0].")"; //The Agent title
					
			$NextAgentID=$data[0]; //The  Agent ID
										
		}
		
		//If adding a new agent
		else {
			
			$title="Add Agent"; //The default title
			$NextAgentID=$largestagentid+1; //Setup for next Agent ID
			$data[0]=$NextAgentID;
			$data[1]="";
			$data[2]="";
			
		}
		
		$info=$title.",".$NextAgentID.",".$data[1].",".$data[0].",".$data[2]; //Returns Agent title, next Agent name, Agent IS, and Agent password.
				
		return $info;	
	
	}	
	        
?>

