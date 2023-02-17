<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信支付示例</title>
</head>

<body>
    <!-- 提交表单 -->
    <form id="my-form" action="goToPay" method="post">
        <img class="black_img" src="__ROOT__/uploads/<?= $resultimage['wxapp_id'] ?>/<?= $resultimage['file_name'] ?>" alt="">
        <span>会员名称：<?= $articleid['article_title'] ?></span>
        <span">会员等级：<?= $resultlevel['name'] ?></span>
        <span>应缴金额：<?= $articleid['cost'] ?></span>
        <input type="hidden" name="people_id" value="<?= $articleid['article_id'] ?>" />
        <button type="sumbit">确认交费</button>
    </form>
</body>

</html>