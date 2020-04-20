<table style="background: rgb(0,51,102);
background: linear-gradient(90deg, rgba(0,51,102,1) 0%, rgba(167,69,36,1) 50%);
color: #ffffff;width:100%;border-radius:4px;padding-left:2%;">
<!--<img src="{{$appurl}}images/logo.png" style="width:15%;"/>-->
<p style="color:#fff;">
Dear {{ $name }},
</p>
<p style="color:#fff;">
Email Address:  {{ $email }}
</p>
<p style="color:#fff;">
Your account has been created. Click on the link below to activate.
</p>
<a href="{{$link}}" style="text-decoration:none;background-color: #4CAF50;
  border: none;
  color: white;
  padding: 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;"><b style="color:#fff;">CLICK TO ACTIVATE</b></a>
<hr>
<p style="color:#fff;">
Thank you for your registering.
</p>
<p style="color:#fff;">&copy; All rights reserved</p>
</table>
