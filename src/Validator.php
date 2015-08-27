<?php

namespace Blurpa\EasyValidator;

use Blurpa\EasyValidator\RuleNotFoundException;

class Validator
{
    /**
     * @var bool
     */
    private $validationStatus;

    /**
     * @var bool
     */
    private $itemValidationStatus;

    /**
     * @var bool
     */
    private $itemStopApplyingRules;

    /**
     * @var string
     */
    private $itemLabel;

    /**
     * @var mixed
     */
    private $itemValue;

    /**
     * @var int
     */
    private $itemTotalRulesApplied;

    /**
     * @var array
     */
    private $messages = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->validationStatus = true;

    }

    /**
     * Initializes the validation parameters of the given $label
     *
     * @param string $itemLabel
     * @param string $itemValue
     *
     * @return $this
     */
    public function validate($itemLabel, $itemValue)
    {
        $this->itemValidationStatus = true;
        $this->itemStopApplyingRules = false;
        $this->itemLabel = $itemLabel;
        $this->itemValue = $itemValue;
        $this->itemTotalRulesApplied = 0;

        return $this;
    }

    /**
     * @param string $ruleName
     * @param string $options
     *
     * @return $this
     */
    public function applyRule($ruleName, $options = '')
    {
        $this->processRule($ruleName, $options);

        return $this;
    }

    /**
     * @param string $ruleName
     * @param string $options
     *
     * @return $this
     */
    public function applyStop($ruleName, $options = '')
    {
        $this->processRule($ruleName, $options);

        if ($this->itemValidationStatus === false) {
            $this->itemStopApplyingRules = true;
        }

        return $this;
    }

    /**
     *
     * @throws RuleNotFoundException when no rule is found.
     *
     * @param string $ruleName
     * @param string $options
     */
    private function processRule($ruleName, $options)
    {
        if ($this->itemStopApplyingRules) {
            return;
        }

        $ruleName = '\Blurpa\EasyValidator\Rules\\' . $ruleName;

        if (!class_exists($ruleName)) {
            throw new RuleNotFoundException;
        }

        $rule = new $ruleName;
        if (!$rule->validate($this->itemValue, $options)) {
            $this->validationStatus = false;
            $this->itemValidationStatus = false;

            $message = $rule->getMessage();
            $message = str_replace('{label}', $this->itemLabel, $message);
            $message = str_replace('{option1}', $options, $message);
            $this->messages[] = $message;
        }

        $this->itemTotalRulesApplied++;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->validationStatus;
    }

    /**
     * @return bool
     */
    public function getRecentItemStatus()
    {
        return $this->itemValidationStatus;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return int
     */
    public function getItemTotalRulesApplied()
    {
        return $this->itemTotalRulesApplied;
    }
}