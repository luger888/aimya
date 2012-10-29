<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <!-- [ header starts here] -->
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <!--<tr>
                         <td valign="top">
                            <a href="{{store url=""}}"><img src="{{skin url="images/logo_email.gif" _area='frontend'}}" alt="{{var store.getFrontendName()}}"  style="margin-bottom:10px;" border="0"/></a>
                        </td>
                    </tr> -->
                    <!-- [ middle starts here] -->
                    <tr>
                        <td valign="top">
                            <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Dear %1$s %2$s,</h1>
                            <p style="font-size:12px; line-height:16px; margin:0 0 16px 0;">Your e-mail %3$s must be confirmed before using it to log in to our store.</p>
                            <p style="font-size:12px; line-height:16px; margin:0 0 8px 0;">To confirm the e-mail and instantly log in, please, use <a href="%6$s/user/confirmation?query_id=%3$s&query_key=%5$s" style="color:#1E7EC8;">this confirmation link</a>. This link is valid only once.</p>
                            <p style="border:1px solid #E0E0E0; font-size:12px; line-height:16px; margin:0 0 16px 0; padding:13px 18px; background:#f9f9f9;">
                                Use the following values when prompted to log in:<br/>
                                <strong>E-mail:</strong> %3$s<br/>
                                <strong>Password:</strong> %4$s<p>
                            <!-- <p style="font-size:12px; line-height:16px; margin:0;">If you have any questions about your account or any other matter, please feel free to contact us at <a href="mailto:{{config path='trans_email/ident_support/email'}}" style="color:#1E7EC8;">{{config path='trans_email/ident_support/email'}}</a> or by phone at {{config path='general/store_information/phone'}}.</p>-->
                        </td>
                    </tr>
                    <!--<tr>
                        <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you again, <strong>{{var store.getFrontendName()}}</strong></p></center></td>
                    </tr>-->
                </table>
            </td>
        </tr>
    </table>
</div>
</body>