<?php

namespace App\Traits;

use Carbon\Carbon;

trait InteractsWithDatetimeInput
{
    protected array $datetimeParseErrors = [];
    protected function convertDatetimeInputs(array $fields): void
    {
        foreach ($fields as $field) {
            if ($this->filled($field)) {
                $raw = $this->input($field);
                if (preg_match('/^\d{2}:\d{2} \d{2}\/\d{2}\/\d{4}$/', $raw)) {
                    try {
                        $carbon = Carbon::createFromFormat('H:i d/m/Y', $raw);
                        if ($carbon->format('H:i d/m/Y') === $raw) {
                            $this->merge([
                                $field => $carbon->format('Y-m-d H:i:s')
                            ]);
                        }
                        else{
                            $this->datetimeParseErrors[$field] = 'Định dạng không đúng: ' . $raw;
                        }
                    } catch (\Exception $e) {
                        $this->datetimeParseErrors[$field] = 'Thời gian không hợp lệ: ' . $raw;
                    }
                }
                else{
                    $this->datetimeParseErrors[$field] = 'Định dạng không đúng: ' . $raw;
                }
            }
        }
    }
}