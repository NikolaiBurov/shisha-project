<?php

namespace App\Http\Services;

class ErrorService
{
    /**
     * @var string
     */
    private string $errors;

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
