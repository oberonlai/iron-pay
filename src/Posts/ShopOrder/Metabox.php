<?php

namespace Irp\Posts\ShopOrder;

use ODS\Metabox;

defined( 'ABSPATH' ) || exit;

$metabox = new Metabox(
	array(
		'id'       => 'iron_pay_field',
		'title'    => '鐵人付交易結果',
		'screen'   => 'shop_order',
		'context'  => 'side',
		'priority' => 'low',
	)
);

$metabox->addText(
	array(
		'id'    => '_irp_resp_code',
		'label' => '回應代號',
	)
);

$metabox->addTextarea(
	array(
		'id'    => '_irp_resp_result',
		'label' => '交易結果',
	)
);

