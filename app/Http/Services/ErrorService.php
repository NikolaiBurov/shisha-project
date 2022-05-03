<?php

namespace App\Http\Services;

class ErrorService
{
    /**
     * @var string
     */
    private string $errors;


    public function handleMissingFields(array $errors)
    {
        if (isset($errors)) {

            $stringable_errors = implode(",", $errors);

            return count($errors) > 1 ? 'The fields ' . $stringable_errors . ' are missing' : 'The field ' . $stringable_errors . ' is missing';
        }
    }

    /**
     * @param array $errors
     * @return string
     */
    public function convertErrors(array $errors = []): string
    {
        if (isset($errors)) {
            foreach ($errors as $item => $content) {
                $var[$item] = $item . ":" . implode(",", $content);
            }
            $this->setErrors(implode("|", $var));
        }

        return $this->getErrors();
    }

    /**
     * @return string
     */
    public function getErrors(): string
    {
        return $this->errors;
    }

    /**
     * @param string $errors
     * @return ErrorService
     */
    public function setErrors(string $errors): ErrorService
    {
        $this->errors = $errors;
        return $this;
    }
}
