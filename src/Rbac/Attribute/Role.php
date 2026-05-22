<?php

namespace BeastBytes\Yii\Rbam\Rbac\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Role extends Item {}