<?php

    if(!isset($_REQUEST['user']) && !isset($_REQUEST['ticket_id']) && !isset($_REQUEST['ticket_number']) && !isset($_REQUEST['license']))       
    { 
        echo 'Invalid Call'; exit();
    }

    error_reporting(E_ALL);
    ini_set('display_errors','On');           
    //TESTING MODE - 1, LIVE MODE - 0
    define('TESTING',1);  
    // Get the directory range
    global $rootpath;    
    $rootpath = $_SERVER['DOCUMENT_ROOT'];    
    if(TESTING)
    {
        $oldhelpdeskConnResponse = mysqli_connect('celcius.egrabbersupport.com', 'celcius_ro', 'M9kRcrv62HUv', 'celcius_helpdesk_database'); 
    //    mysqli_select_db($eregConnection,"registrations");
    }
    else
    {
        include_once($rootpath.'/scripts/dependencies/confreader_v3.php');	
        global $connectionResponse;
        $connectionResponse = 1;    
        $oldhelpdeskConnResponse = connectMYSQL('[OLDHELPDESK]');	
    }
    
    $userIp = "";
    $ticket_id = 0;
    $output = "";
    
    if(  (isset($_REQUEST['user'])) || (isset($_REQUEST['license'])) )
    {
                $userIp = (isset($_REQUEST['user'])) ? (trim($_REQUEST['user'])) : (trim($_REQUEST['license']));
                $query = "";
                if(filter_var($userIp, FILTER_VALIDATE_EMAIL)) 
                {
                        $query = "SELECT * FROM thread INNER JOIN ticket ON ticket.ticket_id = thread.ticket_id WHERE thread_replyto LIKE '%$userIp%' OR thread_to LIKE '%$userIp%'";
                        $results = mysqli_query($oldhelpdeskConnResponse, $query);
                        $totalData = mysqli_num_rows($results);
                        $totalFiltered = $totalData;
                        $data = array();
                        $tickNum = array();
                        $datecreated = array();
                        $lastDate = array();
                        if(!$results ||  mysqli_num_rows($results) > 0)  
                        { 
                            while( $row=mysqli_fetch_array($results) )
                            {
                                $data[] = $row["ticket_id"];
                                $tickNum[] = $row["ticket_mask"];
                                $datecreated[] = $row["ticket_date"];
                                $lastDate[] = $row["ticket_last_date"];
                            }
                            $data = array_unique($data);
                        }
                        else
                        {
                            $output = '<h2 align="center">Data not found</h2>'; 
                            echo $output; 
                            exit();
                        }
                        $length = count($data);
                        if (count($data)>0)
                        {                    
                            $output .= '<table id="table" align="center"><thead><tr><th width="5%">Ticket Id</th><th width="7%">Create Date</th><th width="20%">Subject</th><th width="7%">Last Updated Date</th></tr></thead>';
                            $ticketMask = "";
                            $createDate = "";
                            $lastDates = "";
                            foreach ($data as $value) 
                            {
                                $getTicketMask = "SELECT * FROM ticket WHERE ticket_id = $value";  
                                $getTicketMaskResult = mysqli_query($oldhelpdeskConnResponse, $getTicketMask);
                                while($maskRow=mysqli_fetch_array($getTicketMaskResult) )
                                {
                                  $ticketMask = $maskRow["ticket_mask"];
                                  $createDate = $maskRow["ticket_date"];
                                  $lastDates = $maskRow["ticket_last_date"];
                                }
                                $contentquery = "SELECT * FROM thread INNER JOIN thread_content_part ON thread_content_part.thread_id = thread.thread_id WHERE ticket_id = $value ";
                                $contentresults = mysqli_query($oldhelpdeskConnResponse, $contentquery);
                                $checkThreadId = 0;
                                $content = "";
				                if(!$contentresults || mysqli_num_rows($contentresults) === 0)                                
                                {
                                  continue;
                                }
                                else
                                {
                                  while($rows=mysqli_fetch_array($contentresults) )
                                  {
                                    if ($rows["ticket_id"] == $checkThreadId)
                                    {
                                      continue;
                                    }
                                    else
                                    {   
                                      $output .= '<tr><td class="nr">'. $value .'</td><td class="tn">'. $ticketMask .'</td><td>'. $createDate .'</td><td class="sub">'. $rows["thread_subject"] .'</td><td>'. $lastDates .'</td></tr>';
                                      $checkThreadId = $rows["ticket_id"];
                                    }                                
                                  }
                                }
                            }                     
                        }
                         
                        $output .= '</table>';  
                        echo $output;                 
                } 

               else  
                {
                      $query1 = "SELECT * FROM thread_content_part WHERE thread_content_part LIKE '%$userIp%'";
                      $results1 = mysqli_query($oldhelpdeskConnResponse, $query1);
                      $totalData1 = mysqli_num_rows($results1);
                      $ticketMask = array();
                      $data = array();
                      $tickNum = array();
                      $datecreated = array();
                      $lastDate = array();
                      $threadID = 0;
                      
                      if(!$results1 || mysqli_num_rows($results1) > 0)  
                      {                       
                                while( $row=mysqli_fetch_array($results1) )
                                {                          
                                      $threadID = $row["thread_id"];
                                      $query2 = "SELECT * FROM ticket INNER JOIN thread ON thread.ticket_id = ticket.ticket_id WHERE thread_id = $threadID";
                                      $results2 = mysqli_query($oldhelpdeskConnResponse, $query2);
                                      
                                      while ($innerRows=mysqli_fetch_array($results2))
                                      {
                                        $tickNum [] = $innerRows["ticket_mask"];
                                        $data[] = $innerRows["ticket_id"];
                                        $datecreated[] = $innerRows["ticket_date"];
                                        $lastDate[] = $innerRows["ticket_last_date"];
                                      }
                                }
                                $tickNum = array_unique($tickNum);
                                $data = array_unique($data);
                                $length = count($data);
                                
                                if (count($data)>0)
                                {                     
                                    $output .= '<table id="table" align="center"><thead><tr><th width="5%">Ticket Id</th><th width="7%">Create Date</th><th width="20%">Subject</th><th width="7%">Last Updated Date</th></tr></thead>';
                                    $ticketMask = "";
                                    $createDate = "";
                                    $lastDates = "";
                                    
                                    foreach ($data as $value) 
                                    {
                                        $getTicketMask = "SELECT * FROM ticket WHERE ticket_id = $value";  
                                        $getTicketMaskResult = mysqli_query($oldhelpdeskConnResponse, $getTicketMask);
                                        while($maskRow=mysqli_fetch_array($getTicketMaskResult) )
                                        {
                                            $ticketMask = $maskRow["ticket_mask"];
                                            $createDate = $maskRow["ticket_date"];
                                            $lastDates = $maskRow["ticket_last_date"];
                                        }
                                        
                                        $contentquery = "SELECT * FROM thread INNER JOIN thread_content_part ON thread_content_part.thread_id = thread.thread_id WHERE ticket_id = $value ";
                                        $contentresults = mysqli_query($oldhelpdeskConnResponse, $contentquery);
                                        $checkThreadId = 0;
                                        $content = "";
                                        
                                        if(!$contentresults ||  mysqli_num_rows($contentresults) == 0) 
                                        {
                                            continue;
                                        }
                                        else
                                        {                                  
                                            while($rows=mysqli_fetch_array($contentresults) )
                                            {
                                                if($rows["ticket_id"] == $checkThreadId)
                                                {
                                                    continue;
                                                }
                                                else
                                                {                                          
                                                    $output .= '<tr><td class="nr">'. $value .'</td><td class="tn">'. $ticketMask .'</td><td>'. $createDate .'</td><td class="sub">'. $rows["thread_subject"] .'</td><td>'. $lastDates .'</td></tr>';
                                                    $checkThreadId = $rows["ticket_id"];
                                                }                                      
                                            }
                                        }
                                    }  
                                }                      
                                
                                $output .= '</table>';  
                                echo $output;                      
                      }
                      else
                      {
                          $output = '<h2 align="center">Data not found</h2>'; 
                          echo $output;
                          exit();
                      }
                }
                  
    }
    
    
    else if(isset($_REQUEST['ticket_number']))
    {
            $userIp = (isset($_REQUEST['ticket_number'])) ? (trim($_REQUEST['ticket_number'])) : '';
            $query = "SELECT DISTINCT ticket_id,ticket_mask,ticket_date,ticket_last_date  FROM ticket WHERE ticket_mask LIKE '%$userIp%'";   
            $results = mysqli_query($oldhelpdeskConnResponse, $query);
            $data = array();
            $tickNum = array();
            $datecreated = array();
            $lastDate = array();
            $ticket_id=0;
            
            if(!$results || mysqli_num_rows($results) > 0)  
            {
                while( $row=mysqli_fetch_array($results) )
                {
                  $data[] = $row["ticket_id"];
                  $tickNum[] = $row["ticket_mask"];
                  $datecreated[] = $row["ticket_date"];
                  $lastDate[] = $row["ticket_last_date"];                
                }                
            }
            else
            {
                $output = '<h2 align="center">Data not found</h2>'; 
                echo $output; 
                exit();
            }            
            $length = count($data);
            if (count($data)>0)
            {
                $output .= '<table id="table" align="center"><thead><tr><th width="5%">Ticket Id</th><th width="7%">Create Date</th><th width="20%">Subject</th><th width="7%">Last Updated Date</th></tr></thead>';
                for ($i = 0; $i < $length; $i++)
                {
                    $value = $data[$i];
                    $ticketVal = $tickNum[$i];
                    $createDate = $datecreated[$i];
                    $lastDates = $lastDate[$i];
                    $contentquery = "SELECT * FROM thread INNER JOIN thread_content_part ON thread_content_part.thread_id = thread.thread_id WHERE ticket_id = $value";
                    $contentresults = mysqli_query($oldhelpdeskConnResponse, $contentquery);
                    $checkThreadId = 0;
                    $content = "";
                    
		            if(!$contentresults || mysqli_num_rows($contentresults) == 0)
                    {
                       continue;
                    }
                    else
                    {
                      while($rows=mysqli_fetch_array($contentresults) )
                      {
                        if ($rows["ticket_id"] == $checkThreadId)
                        {
                          continue;
                        }
                        else
                        {
                          $output .= '<tr><td class="nr">'. $value .'</td><td class="tn">'. $ticketVal .'</td><td>'. $createDate .'</td><td class="sub">'. $rows["thread_subject"] .'</td><td>'. $lastDates .'</td></tr>';
                          $checkThreadId = $rows["ticket_id"];
                        }
                      }
                    }
                }           
            }          
            $output .= '</table>';  
            echo $output;   
    }    
    else if(isset($_REQUEST['ticket_id']))
    {
            $output1 = "";
            $ticket_id = (isset($_REQUEST['ticket_id'])) ? (trim($_REQUEST['ticket_id'])) : '';
            
            $output .= '<div>';
            $contentquery = "SELECT * FROM thread INNER JOIN thread_content_part ON thread_content_part.thread_id = thread.thread_id WHERE ticket_id = $ticket_id";
            $contentresults = mysqli_query($oldhelpdeskConnResponse, $contentquery);
            $checkThreadId = 0;
            $content = "";
            if(!$contentresults || mysqli_num_rows($contentresults) == 0)
            {
                $output = 'Data not found'; 
                echo $output; 
                exit();
            }
            
            $datas=array(); 
            $checkThreadId = 0;
            $contentId = 0;
            $from = "";
            $to = "";
            $subject = "";
            while($rows=mysqli_fetch_array($contentresults) )
            {
                if ($checkThreadId == $rows["thread_id"])
                {
                  $output1 .= '<div id="msg1"><pre>'.$rows["thread_content_part"].'</pre></div>';
                  $output = '<div>'.$output1 .'</div>';
                  $checkThreadId = $rows["thread_id"];
                }
                else
                {
                  $to = $rows["thread_to"];
                  $output1 .= '<div class ="border-color">';
                  $output1 .= '<h3>From : ' . $rows["thread_replyto"] .'</h3>';
                  $output1 .= '<h3>Subject : '.$rows["thread_subject"] . '</h3>';
                  $to = trim($to,'<>');
                  $output1 .= '<h3>To : '.$to . '</h3>';
                  $output1 .= '<h3>Date : '.$rows["thread_received"] . '</h3></div>';
                  $output1 .= '<div id="msg1"><pre>'.$rows["thread_content_part"].'</pre></div>';
                  $output = '<div>'.$output1 .'</div>';
                  $contentId = $rows["content_id"];
                  $checkThreadId = $rows["thread_id"];
                  $from = $rows["thread_replyto"];
                  $to = $rows["thread_to"];
                  $subject = $rows["thread_subject"];                
                }
            }
            $output .= '</div>';
            echo $output;   
    }   
    mysqli_close($oldhelpdeskConnResponse);
?>