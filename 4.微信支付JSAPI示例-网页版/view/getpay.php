<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>订单支付</title>
    <script src="__MOBILE__/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="__MOBILE__/js/layer.js?t=<?=time()?>"  type="text/javascript" ></script>
    <style type="text/css">
        *{ margin:0; padding:0}
        .wihe-ee{ background:#FFF; padding:25px; color:#666; font-family:song,arial; font-size:14px; margin:0 auto; text-align:center}
        .wihe-ee p{text-align:center}
        .fail-I-success{margin-top:30px;border-bottom:1px solid #d8d8d8; padding-bottom:30px}
        .co999{color:#999}
        .fo-si-18{font-size:18px}
        .fail-fasu{ text-align:center; border-bottom:1px solid #d8d8d8; padding-bottom:30px}
        .success-fasu{margin-top:30px;  text-align:center}
        .fail-fasu a:hover{ background-color:#ee9775}
        .fail-fasu a{padding:8px 24px; background-color:#f8a584; display:table; margin:0 auto; color:#fff; text-decoration:none; margin-top:10px}
        .re-qtzfgg a,.success-fasu a{padding:8px 24px; background-color:#eee; display:table; margin:0 auto; color:#999; text-decoration:none; margin-top:10px}
        .re-qtzfgg a:hover,.success-fasu a:hover{background-color:#ddd;}
    </style>
</head>
<body style="max-width:640px; min-width:320px; margin:0 auto">
<div class="tac-sd">
    <div class="wihe-ee">
        <p>
            <span class="fo-si-18">请您在新打开的页面上完成付款!</span>
            <br>
            <span class="co999">付款完成前请不要关闭此窗口。完成付款后请根据您的情况点击下面的按钮。</span>
        </p>
        <br>
        <br>

        <div class="fail-fasu">
            <img src="__ROOT__/assets/mobile/images/suc_cg.png"/>
            <p>支付完成</p>
            <br>
            <a href="<?= $url[0] ?>">支付成功</a>
        </div>
        <div class="success-fasu">
            <img src="__ROOT__/assets/mobile/images/suc_sb.png"/>
            <p>支付遇到问题</p>
            <br>
            <a href="<?= $url[1] ?>">支付失败</a>
        </div>

    </div>
</div>
<?= $html;?>
</body>
</html>
