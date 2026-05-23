<?php

namespace App\Notifications;

use PiaCore\Notifications\BaseNotification;

class ImportCompleted extends BaseNotification
{
    /**
     * @param int   $success Number of successfully imported rows
     * @param array<int, string> $errors  Row-number-keyed error messages
     */
    public function __construct(
        protected int $success,
        protected array $errors = [],
    ) {}

    public function type(): string
    {
        return 'import.completed';
    }

    public function toDatabase(): array
    {
        $rowErrors = [];
        foreach ($this->errors as $row => $message) {
            $rowErrors[] = [
                'row' => $row,
                'message' => $message,
            ];
        }

        return [
            'title' => 'Import Completed',
            'message' => "Imported {$this->success} record(s). "
                . (count($this->errors) > 0 ? count($this->errors) . ' error(s).' : 'No errors.'),
            'success_count' => $this->success,
            'error_count' => count($this->errors),
            'row_errors' => $rowErrors,
            'action_url' => '/import-export',
        ];
    }

    public function toMail(): array
    {
        $errorCount = count($this->errors);

        // Plain text
        $body = "Employee import completed.\n\n"
            . "Successfully imported: {$this->success}\n"
            . "Errors: {$errorCount}";

        if ($errorCount > 0) {
            $body .= "\n\nError details:\n";
            foreach ($this->errors as $row => $error) {
                $body .= "  Row {$row}: {$error}\n";
            }
        }

        // HTML with layout
        $errorBlock = '';
        if ($errorCount > 0) {
            $rows = '';
            foreach ($this->errors as $row => $error) {
                $rows .= '<tr style="border-bottom: 1px solid #f1f5f9;">'
                    . '<td style="padding: 8px 12px; font-family: monospace; color: #64748b; vertical-align: top;">R' . $row . '</td>'
                    . '<td style="padding: 8px 12px; color: #334155;">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</td>'
                    . '</tr>';
            }
            $errorBlock = '<h3 style="color: #991b1b; margin-top: 24px;">Error Details</h3>'
                . '<table style="width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 14px;">'
                . '<thead><tr style="background: #fef2f2; border-bottom: 2px solid #fecaca;">'
                . '<th style="padding: 10px 12px; text-align: left; color: #991b1b; font-weight: 600; width: 80px;">Row</th>'
                . '<th style="padding: 10px 12px; text-align: left; color: #991b1b; font-weight: 600;">Error</th>'
                . '</tr></thead>'
                . '<tbody>' . $rows . '</tbody></table>';
        }

        $summaryColor = $errorCount > 0 ? '#fef2f2' : '#f0fdf4';
        $summaryTextColor = $errorCount > 0 ? '#991b1b' : '#166534';
        $summaryBorder = $errorCount > 0 ? '#fecaca' : '#bbf7d0';

        $html = '<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">'
            . '<tr>'
            . '<td style="padding: 12px; background: ' . $summaryColor . '; border: 1px solid ' . $summaryBorder . '; border-radius: 6px; text-align: center;">'
            . '<span style="font-size: 24px; font-weight: 700; color: ' . $summaryTextColor . ';">' . $this->success . '</span>'
            . '<br><span style="color: ' . $summaryTextColor . '; font-size: 13px;">imported</span>'
            . '</td>'
            . '<td style="width: 16px;"></td>'
            . '<td style="padding: 12px; background: ' . ($errorCount > 0 ? '#fef2f2' : '#f8fafc') . '; border: 1px solid ' . ($errorCount > 0 ? '#fecaca' : '#e2e8f0') . '; border-radius: 6px; text-align: center;">'
            . '<span style="font-size: 24px; font-weight: 700; color: ' . ($errorCount > 0 ? '#991b1b' : '#64748b') . ';">' . $errorCount . '</span>'
            . '<br><span style="color: ' . ($errorCount > 0 ? '#991b1b' : '#64748b') . '; font-size: 13px;">errors</span>'
            . '</td>'
            . '</tr>'
            . '</table>'
            . $errorBlock
            . '<p style="text-align: center; margin-top: 24px;">'
            . '<a href="' . url('/import-export') . '" class="btn">View Import Results</a></p>';

        return [
            'subject' => 'Import Completed - ' . config('app.name'),
            'body'    => $body,
            'html'    => $html,
        ];
    }
}
