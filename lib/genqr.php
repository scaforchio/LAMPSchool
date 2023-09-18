<?php
require_once('../lib/phpqrcode/qrlib.php');
QRCode::png("otpauth://totp/Giustifica?secret=".$_GET["code"]."&issuer=LampSchool");