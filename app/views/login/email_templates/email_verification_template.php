<!DOCTYPE html>
<html lang="en">
<head>
<title></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<!--
    Esto es un ejemplo de Template para correo
-->

<style type="text/css">

    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    /* Prevent WebKit and Windows mobile changing default text sizes */
  
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    /* Remove spacing between tables in Outlook 2007 and up */

    img { -ms-interpolation-mode: bicubic; }
    /* Allow smoother rendering of resized image in Internet Explorer */

    /* RESET STYLES */
    img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    table { border-collapse: collapse !important; }
    body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* ---------------- */
    /* HIGHLIGHT STYLES */
    /* ---------------- */

    /* FIRST TABLE: Highlights the entire table row when hovering over the first column */
    /* These styles targets Gmail and Yahoo! email clients */
    .row:hover, .row:hover + td, .row:hover + td + td, .row:hover + td + td + td,

    /* Following code highlights individual table cells */
    .row + td:hover, .row + td + td:hover, .row + td + td + td:hover {
        background-color: #e6b4f6 !important;
    }


    /* Specific styles for Outlook.com */
    /* For Outlook.com, the class must be followed by an HTML element; cannot be class:hover or id:hover */
    .outlookRow td:hover {
        background-color: #e6b4f6 !important;
    }

    /* Figure out where the breaks happen and use that in the media query */
    @media (max-width: 800px) {
        .table-container {
            font-size: 66% !important;
        }
    }

    @media (max-width: 400px) {
        .table-container {
            font-size: 40% !important;
        }
    }

    
</style>
</head>

    <body bgcolor="#ffffff" style="margin: 0 !important; padding: 0 !important; background-color: #ffffff;">

        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="600">

            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: auto;" >
                <?php
                    if (isset($image_header)){
                        echo "<tr>
                            <td style=\"padding: 20px 0; text-align: center\">
                                <img src=\"{$image_header['src']}\" width=\"{$image_header['width']}\" height=\"{$image_header['height']}\" border=\"0\" style=\"height: auto;  font-family: sans-serif; ; line-height: 15px; color: #555555;\">
                            </td>
                        </tr>";
                    }
                ?>

                <!-- Email Header : BEGIN -->
                
                <!-- Email Header : END -->

                <!-- Hero Image, Flush : BEGIN -->
                <!-- <tr>
                    <td style="background-color: #ffffff;" class="darkmode-bg">
                        <img src="https://brimell.cl/wp-content/uploads/2022/01/cropped-cropped-BRIMELLtransparenteV01.png" width="600" height="" alt="alt_text" border="0" style="width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;" class="g-img">
                    </td>
                </tr> -->
                <!-- Hero Image, Flush : END -->
            </table>

             <!-- SECOND TABLE -->
             <tr>
                <td align="center" valign="top" width="100%" bgcolor="#ffffff" style="background-color: #ffffff;">
                    <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="598" class="table-container">
                        <tr>
                            <td>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="left" valign="top" bgcolor="#ffffff" style="background-color: #ffffff; border-top: 1px solid #828282; margin: 0; padding: 40px 0 40px 0; width: 100%;">
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                                                <tr>
                                                    <td align="left" valign="middle" width="100%" colspan="4" style="color: #000000; font-weight: 600; font-size: 120%; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0;">COTIZACIÓN ENVIO</td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="middle" width="100%" colspan="4" style="color: #000000; font-weight: 400; font-size: 100%; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding-top: 5px;"><span style="font-weight: 600;">Fecha</span>: <?= $datetime ?></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td height="20" colspan="2"></td>
                                                </tr>

                                                <!-- INFO -->
                                                <?php
                                                    foreach ($cols as $ix => $col){
                                                        ?>
                                                            <td align="left" valign="middle" width="<?= $withs[$ix] ?>%" style="background-color: #df78ef; border: 1px solid #d05ce3; color: #ffffff; font-weight: 600; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 10px;"><?= $col ?></td>
                                                        <?php
                                                    }
                                                ?>

                                                <!-- DATA -->
                                                <?php

                                                    foreach ($rows as $ix => $row){
                                                        echo "<tr class=\"outlookRow\">";                                                        
                                                        foreach ($row as $ij => $cell){
        
                                                            if ($ij === 0){
                                                                $class = "class=\"row\"";
                                                                $fw = 600;
                                                            } else {
                                                                $class = '';    
                                                                $fw = 300;
                                                            }

                                                            echo "<td $class align=\"left\" valign=\"middle\" style=\"border: 1px solid #d05ce3; color: #000000; font-weight: $fw;  font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; padding: 10px;\"><a href=\"#\" style=\"color: #000000; text-decoration: none;\">{$cell}</a></td>";
                                                        
                                                        }                                                        
                                                        echo '</tr>';
                                                    }
                                                ?>

                                                 <!-- TOTAL MONEDA LOCAL -->
                                                <tr class="outlookRow">
                                                    <td align="left" valign="middle"  style="color: #ffffff; font-weight: 600; font-size: 100%; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; background-color:#df78ef; padding: 10px; border: 1px solid #ffffff;">TOTAL</td>

                                                    <td align="left" valign="middle"   style="color: #ffffff; font-weight: 400; font-size: 100%; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; background-color:#df78ef; padding: 10px; border: 1px solid #ffffff;"><?= $total_local_currency ?></td>   

                                                    <td align="left" valign="middle"  style="color: #ffffff; font-weight: 400; font-size: 100%; font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.5em; margin: 0; background-color:#df78ef; padding: 10px; border: 1px solid #ffffff;"><?= $local_currency_quote ?></td>      
                                                </tr> 

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- end second table -->
        </table>

       


        </table>

        <!-- Full Bleed Background Section : BEGIN -->
	    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #43494e;" class="table-container darkmode-fullbleed-bg">
	        <tr>
	            <td>
	                <div align="center" style="max-width: 600px; margin: auto;" class="email-container">
	                    <!--[if mso]>
	                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center">
	                    <tr>
	                    <td>
	                    <![endif]-->
	                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
	                        <tr>
	                            <td style="padding: 20px; text-align: center; font-family: sans-serif; line-height: 10px; color: #ffffff;">
	                                <p style="margin: 0;"><?= $footer ?></p>
	                            </td>
	                        </tr>
	                    </table>
	                    <!--[if mso]>
	                    </td>
	                    </tr>
	                    </table>
	                    <![endif]-->
	                </div>
	            </td>
	        </tr>
	    </table>
	    <!-- Full Bleed Background Section : END -->

       
    </body>
</html>