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

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\CountryPageInterface;
use CoreShop\Behat\Page\Pimcore\CoreShop\CurrencyPageInterface;
use CoreShop\Behat\Page\Pimcore\CoreShop\ExchangeRatePageInterface;
use CoreShop\Behat\Page\Pimcore\CoreShop\StatePageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class StateContext implements Context
{
    private $pwaPage;
    private $statePage;

    public function __construct(
        PWAPageInterface $pwaPage,
        StatePageInterface $statePage
    )
    {
        $this->pwaPage = $pwaPage;
        $this->statePage= $statePage;
    }

    /**
     * @When states tab is open
     */
    public function statesTabIsOpen(): void
    {
        Assert::true($this->statePage->isActiveOpen());
    }
}
