<table class="divider old-background-image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; vertical-align: top; border-spacing: 0px; border-collapse: collapse; min-width: 100%; text-size-adjust: 100%; background-image: none;" role="presentation" valign="top">
    <tbody>
    <tr style="vertical-align: top; background-image: none;" valign="top" class="old-background-image">
        <td class="divider_inner old-background-image" style="word-break: break-word; vertical-align: top; min-width: 100%; text-size-adjust: 100%; padding: 10px; background-image: none;" valign="top">
            <table class="divider_content old-background-image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; vertical-align: top; border-spacing: 0px; border-collapse: collapse; border-top: 1px solid rgb(187, 187, 187); width: 100%; background-image: none;" align="center" role="presentation" valign="top">
                <tbody>
                <tr style="vertical-align: top; background-image: none;" valign="top" class="old-background-image">
                    <td style="word-break: break-word; vertical-align: top; text-size-adjust: 100%; background-image: none;" valign="top" class="old-background-image"><span></span></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
<div style="color: rgb(85, 85, 85); font-family: Arial, &quot;Helvetica Neue&quot;, Helvetica, sans-serif; line-height: 1.2; padding: 10px; background-image: none;" class="old-background-image">
    <div class="txtTinyMce-wrapper old-background-image" style="font-size: 14px; line-height: 1.2; color: rgb(85, 85, 85); font-family: Arial, &quot;Helvetica Neue&quot;, Helvetica, sans-serif; background-image: none;">
        <?php if (isset($sale)) { ?>
            <p style="margin: 0px; font-size: 16px; line-height: 1.2; word-break: break-word; background-image: none; color: #ff4c30;"><strong>Снижение цены!!!</strong></p>
            <p style="margin: 10px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;"></p>
        <?php }?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo $customText ?></p>
        <?php if ($type == 'rent') { ?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><strong>Аренда:</strong></p>

        <?php }?>
        <?php if ($type == 'buy') { ?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image">&nbsp;</p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><strong>Продажа:</strong></p>
        <?php }?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($address) ? 'Адрес: '.$address : '';?></p>
        <?php if (!empty($link_panoram)) { ?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image">
            <?php echo 'Ссылка на панораму: ' ?> <a target="_blank" href="<?php echo  $link_panoram ?>"><?php echo  $link_panoram ?></a>
        </p>
        <?php }?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($area) ? 'Площадь: '.$area.' кв.м.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($metro) ? 'Метро рядом: :'.$metro : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($metro_on_foot) ? 'До метро пешком: :'.$metro_on_foot.' мин.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($stage) ? 'Этаж/этажность: '.$stage.'.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($ceiling) ? 'Высота потолков: '.$ceiling.'.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($enter) ? 'Вход: '.$enter.'.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($electric) ? 'Электрическая мощность: '.$electric.'.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($ventilation) ? 'Вентиляция: '.$ventilation.'.' : '';?></p>
        <p style="margin: 10px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;"></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><strong>Коммерческие условия:</strong></p>
        <?php if ($type == 'rent') { ?>
            <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($rent_month) ? 'Аренда в месяц: '.$rent_month.'.' : '';?></p>
            <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($rent_month_meter) ? 'Аренда в месяц кв.м: '.$rent_month_meter.'.' : '';?></p>
        <?php }?>
        <?php if ($type == 'buy') { ?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($buy_price) ? 'Продажа: '.$buy_price.'.' : '';?></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><?php echo !empty($buy_price_meter) ? 'Продажа кв.м: '.$buy_price_meter.'.' : '';?></p>
        <?php }?>
        <p style="margin: 10px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;"></p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image"><strong>Дополнительно:</strong></p>
        <?php if (isset($kpLinks) && !empty($kpLinks)) { ?>
        </p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;">Ссылка на полное КП: {$kplink}</p>
        <?php }?>
        <?php if (isset($property_photo) && !empty($property_photo)) { ?>
        </p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;">Фото объекта: {{$property_photo}}</p>
        <?php }?>
        <?php if (isset($traffic_video) && !empty($traffic_video)) { ?>
        </p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;">Видео (пешеходный трафик): {{$traffic_video}}</p>
        <?php }?>
        <?php if (isset($review_video) && !empty($review_video)) { ?>
        </p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;">Видео (обзорное): {{$review_video}}</p>
        <?php }?>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;" class="old-background-image">
            <strong>Описание:</strong></p>
            <?php echo !empty($description_additional) ? $description_additional.'.' : '';?>
        </p>
        <p style="margin: 0px; font-size: 14px; line-height: 1.2; word-break: break-word; background-image: none;">&nbsp;</p>
    </div>
</div>
