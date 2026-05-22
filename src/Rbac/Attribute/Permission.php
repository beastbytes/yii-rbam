<?php

namespace BeastBytes\Yii\Rbam\Rbac\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Permission extends Item {}