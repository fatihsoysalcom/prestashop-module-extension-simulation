<?php

/**
 * This example simulates how a core application, like PrestaShop, can be extended
 * using modules. It demonstrates how different modules can be registered, installed,
 * and hooked into the core system's events to add new features or modify existing behavior.
 * The output shows how the system's functionality changes dynamically based on the active modules.
 */

// --- Core PrestaShop Simulator --- 

/**
 * Defines the contract for any module that wants to integrate with the PrestaShop simulator.
 * This interface ensures all modules have basic methods for identification, installation, 
 * uninstallation, and hooking into core events.
 */
interface PrestaShopModuleInterface {
    public function getName(): string;
    public function install(): void;
    public function uninstall(): void;
    // Simulates a 'hook' where modules can inject their logic or output.
    // PrestaShop uses hooks (e.g., displayProductPage, actionValidateOrder) extensively.
    public function executeHook(string $hookName, array $params = []): string;
}

/**
 * Represents the core PrestaShop system, responsible for managing and executing modules.
 * It maintains a list of active modules and provides a mechanism to trigger hooks.
 */
class PrestaShopCoreSimulator {
    private array $modules = [];

    /**
     * Registers a module with the core system and simulates its installation process.
     * This is where a "Jungle" module would be activated and integrated.
     */
    public function registerModule(PrestaShopModuleInterface $module): void {
        $this->modules[$module->getName()] = $module;
        $module->install(); // Simulate installation steps (e.g., DB setup, config)
        echo "CORE: Module '{$module->getName()}' registered and installed.
";
    }

    /**
     * Unregisters a module, simulating its uninstallation and removal from the system.
     */
    public function unregisterModule(string $moduleName): void {
        if (isset($this->modules[$moduleName])) {
            $this->modules[$moduleName]->uninstall(); // Simulate uninstallation steps
            unset($this->modules[$moduleName]);
            echo "CORE: Module '{$moduleName}' unregistered and uninstalled.
";
        }
    }

    /**
     * Executes a specific hook, allowing all registered modules to respond to it.
     * The output from each module for this hook is collected and returned.
     */
    public function executeHook(string $hookName, array $params = []): string {
        $output = "";
        echo "CORE: Executing hook '{$hookName}'...
";
        foreach ($this->modules as $module) {
            $moduleOutput = $module->executeHook($hookName, $params);
            if ($moduleOutput) {
                $output .= "  [{$module->getName()}] " . $moduleOutput . "
";
            }
        }
        return $output;
    }

    public function getModules(): array {
        return array_keys($this->modules);
    }
}

// --- Example "Jungle" Modules ---
// These modules represent the less-known but high-potential extensions discussed in the article.

/**
 * A simple module simulating a loyalty points program.
 * It hooks into product and order processes to add loyalty-related features.
 */
class LoyaltyProgramModule implements PrestaShopModuleInterface {
    public function getName(): string {
        return "LoyaltyProgram";
    }

    public function install(): void {
        echo "  LoyaltyProgram: Initializing loyalty points system and database tables.
";
    }

    public function uninstall(): void {
        echo "  LoyaltyProgram: Disabling loyalty points system and cleaning up data.
";
    }

    public function executeHook(string $hookName, array $params = []): string {
        if ($hookName === 'displayProductPage') {
            // Adds information to the product page
            return "Earn " . round(($params['price'] ?? 0) * 0.05) . " loyalty points with this product!";
        }
        if ($hookName === 'processOrder') {
            // Modifies order processing behavior
            return "Applied 10% discount for loyalty members and awarded points.";
        }
        return "";
    }
}

/**
 * A module for advanced product filtering, enhancing the default category page.
 * This adds functionality that might not be present in the core system.
 */
class AdvancedProductFilterModule implements PrestaShopModuleInterface {
    public function getName(): string {
        return "AdvancedProductFilter";
    }

    public function install(): void {
        echo "  AdvancedProductFilter: Setting up dynamic filter options for categories.
";
    }

    public function uninstall(): void {
        echo "  AdvancedProductFilter: Removing filter configurations and cache.
";
    }

    public function executeHook(string $hookName, array $params = []): string {
        if ($hookName === 'displayCategoryPage') {
            // Injects new UI elements or logic into the category page
            return "Added advanced filters for 'Color', 'Material', 'Brand' to the sidebar.";
        }
        return "";
    }
}

/**
 * A module for marketing automation, integrating with external services or adding internal logic.
 * It hooks into order processing and product display to enhance marketing efforts.
 */
class MarketingAutomationModule implements PrestaShopModuleInterface {
    public function getName(): string {
        return "MarketingAutomation";
    }

    public function install(): void {
        echo "  MarketingAutomation: Connecting to email marketing service and CRM.
";
    }

    public function uninstall(): void {
        echo "  MarketingAutomation: Disconnecting from external services.
";
    }

    public function executeHook(string $hookName, array $params = []): string {
        if ($hookName === 'processOrder') {
            // Triggers actions post-order
            return "Sent order confirmation email and added customer to 'New Buyers' segment.";
        }
        if ($hookName === 'displayProductPage' && isset($params['productName'])) {
            // Adds dynamic content to product pages
            return "Showing 'Customers also bought' recommendations for {$params['productName']}.";
        }
        return "";
    }
}


// --- Main Application Logic (Simulating PrestaShop Lifecycle) ---
echo "--- PrestaShop Core Initialization ---
";
$shop = new PrestaShopCoreSimulator();

echo "
--- Registering 'Jungle' Modules ---
";
// This section demonstrates how modules extend the core functionality.
// The article talks about "niş, daha az bilinen ama potansiyeli yüksek" 
// (niche, less known but high potential) modules.
// These modules add specific, valuable features to the simulated shop.
$shop->registerModule(new LoyaltyProgramModule());
$shop->registerModule(new AdvancedProductFilterModule());
$shop->registerModule(new MarketingAutomationModule());

echo "
--- Simulating User Interactions with Modules Active ---
";

echo "
Scenario 1: Viewing a product page (e.g., 'Super Widget', price 100)
";
// Modules can add information or modify the display of a product page.
echo $shop->executeHook('displayProductPage', ['productName' => 'Super Widget', 'price' => 100]);

echo "
Scenario 2: Viewing a category page (e.g., 'Electronics')
";
// Modules can enhance navigation or filtering options on category pages.
echo $shop->executeHook('displayCategoryPage', ['categoryName' => 'Electronics']);

echo "
Scenario 3: A customer places an order (e.g., Order ID 123)
";
// Modules can trigger post-order actions like sending emails, updating loyalty points, etc.
echo $shop->executeHook('processOrder', ['orderId' => 123, 'customerId' => 456]);

echo "
--- Unregistering a Module (e.g., AdvancedProductFilter) ---
";
// This shows how modules can be removed, reverting the system to its previous state
// or removing the added functionality.
$shop->unregisterModule("AdvancedProductFilter");

echo "
--- Simulating User Interactions After Module Removal ---
";
echo "
Scenario 4: Viewing a category page again (filters from AdvancedProductFilter should be gone)
";
// The output for 'displayCategoryPage' should now be empty from AdvancedProductFilter.
echo $shop->executeHook('displayCategoryPage', ['categoryName' => 'Electronics']);

echo "
--- Current Active Modules ---
";
echo "Active modules: " . implode(", ", $shop->getModules()) . "
";

?>