<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleValidationProcessor implements RuleValidationProcessorInterface
{
    private $ruleConditionsValidationProcessor;

    public function __construct(RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor)
    {
        $this->ruleConditionsValidationProcessor = $ruleConditionsValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $params = []): bool
    {
        return $this->ruleConditionsValidationProcessor->isValid($subject, $rule, $rule->getConditions(), $params);
    }
}
