<?php

namespace PhalconRest\Validation;

use Phalcon\Validation;

/**
 * This is a wrapper around the Phalcon Validation class
 * It provides a clean way to add rules to your models using
 * just an array with field validation rules.
 *
 * 02-04-2015 - Olivier Andriessen.
 */
class Validator
{
    protected $passes = false;
    protected $validators = [];

    /**
     * Create a new Validator instance.
     *
     * @param $data, array $rules
     * @return object
     */
    public function __construct($data, array $rules)
    {

        $this->data = $data;
        $rules = $this->explodeRules($rules);
        $this->rules = array_map([$this, 'parseRules'], $rules);

    }

    /**
     * Explode the rules into an array of rules
     *
     * @param string|array $rules
     * @return array
     */
    protected function explodeRules($rules)
    {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    /**
     * Parses each rule then merges them together
     *
     * @param string $rule;
     * @return array
     */
    protected function parseRules($rule)
    {

        $parsed_rules = array_map([$this, 'parseRule'], $rule);
        return call_user_func_array('array_merge', $parsed_rules);
    }

    /**
     * Explodes the rule into rule name and rule value
     *
     * @param string $rule
     * @return array
     */
    protected function parseRule($rule)
    {

        preg_match_all('/^[a-z]+|\/.*\/|[^:]+/', $rule, $matches);
        return [$matches[0][0] => isset($matches[0][1]) ? $matches[0][1] : null];
    }

    /**
     * Returns the right Phalcon validator class
     *
     * @param string $validator_name
     * @return string
     */
    protected function getValidator($validator_name)
    {

        $validators = [
            'Email' => 'Phalcon\Validation\Validator\Email', // email
            'PresenceOf' => 'Phalcon\Validation\Validator\PresenceOf', // required
            'StringLength' => 'Phalcon\Validation\Validator\StringLength', // min, max
            'Regex' => 'Phalcon\Validation\Validator\Regex', // pattern
        ];

        return $validators[$validator_name];
    }

    /**
     * Keeps validators array up to date with validators
     * and configurables
     *
     * @param string $validator_name, string $opt, string $val
     */
    protected function configureValidator($validator_name, $opt, $val)
    {

        // Validators based on the rules will
        // be added to this array
        $validator = $this->getValidator($validator_name);

        $this->validators[$validator][$opt] = $val;
    }

    /**
     * Clears validators that were keeping track of
     */
    protected function clearValidators()
    {

        $this->validators = [];
    }

    /**
     * Validates the model's data with the
     * configured validators
     *
     * @return boolean
     */
    public function validate()
    {

        // Initialize new Phalcon validation class
        $validation = new Validation;

        foreach ($this->rules as $field => $rules) {

            // For each rule we find out which validator
            // we need and add configuration to them
            foreach ($rules as $rule_name => $rule_value) {

                switch ($rule_name) {
                    case 'min':
                    case 'max':
                        $this->configureValidator('StringLength', $rule_name, $rule_value);
                        break;
                    case 'pattern':
                        $this->configureValidator('Regex', $rule_name, $rule_value);
                        break;
                    case 'email':
                        $this->configureValidator('Email', $rule_name, $rule_value);
                        break;
                    case 'required':
                        $this->configureValidator('PresenceOf', $rule_name, $rule_value);
                        break;
                }
            }

            // Now, let's configure the actual validators
            foreach ($this->validators as $validator => $opts) {

                $validation->add($field, new $validator($opts));
            }

            $this->clearValidators();
        }

        $this->passes = $validation->validate($this->data);

        return !count($this->passes);
    }

    /**
     * Validates and returns true if passes
     * @return boolean
     */
    public function passes()
    {

        return $this->validate();
    }

    /**
     * Validates and returns true if fails
     * @return boolean
     */
    public function fails()
    {

        return !$this->validate();
    }

    /**
     * Returns the validation object
     * which holds messages when validation has not passed
     *
     * @return object
     */
    public function getMessages()
    {
        return $this->passes;
    }

    /**
     * Returns the first message
     * only if validation has failed
     *
     * @return string
     */
    public function getFirstMessage()
    {

        foreach ($this->passes as $message) {
            return $message->getMessage();
        }

        return null;
    }

    /**
     * Returns new instance of this class
     *
     * @param $data, array $rules
     * @return object
     */
    public static function make($data, $rules)
    {
        return new static($data, $rules);
    }
}
