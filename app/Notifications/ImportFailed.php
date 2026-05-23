<?php

namespace App\Notifications;

use PiaCore\Notifications\BaseNotification;

class ImportFailed extends BaseNotification
{
    /**
     * @param int   $errorCount Total number of failed rows
     * @param array<int, string> $errors    Row-number-keyed error messages
     * @param string|null $source   'file' | 'validation' | 'logic' — where the errors came from
     */
    public function __construct(
        protected int $errorCount,
        protected array $errors = [],
        protected ?string $source = null,
    ) {}

    public function routeKey(): string
    {
        return 'import_failed';
    }

    public function toDatabase(): array
    {
        // Group errors by row for structured DB storage
        $rowErrors = [];
        foreach ($this->errors as $row => $message) {
            $rowErrors[] = [
                'row' => $row,
                'message' => $message,
            ];
        }

        return [
            'title' => 'Import Failed',
            'message' => count($this->errors) > 0
                ? "Import failed — {$this->errorCount} row(s) with errors."
                : "Import failed with {$this->errorCount} error(s).",
            'error_count' => $this->errorCount,
            'source' => $this->source ?? 'logic',
            'row_errors' => $rowErrors,
            'action_url' => '/import-export',
        ];
    }

    public function toMail(): array
    {
        $subject = match ($this->source) {
            'validation' => 'Import Failed — Validation Errors — ' . config('app.name'),
            'file'       => 'Import Failed — File Error — ' . config('app.name'),
            default      => 'Import Failed — ' . config('app.name'),
        };

        // Build plain text body
        $body = "Import completed with {$this->errorCount} error(s).\n\n";

        if ($this->source) {
            $sourceLabel = match ($this->source) {
                'validation' => 'Validation Errors',
                'file'       => 'File Error',
                'logic'      => 'Logic Errors',
                default      => ucfirst($this->source),
            };
            $body .= "Source: {$sourceLabel}\n\n";
        }

        if (! empty($this->errors)) {
            $body .= "Failed rows:\n";
            foreach ($this->errors as $row => $error) {
                $body .= "  Row {$row}: {$error}\n";
            }
        }

        $body .= "\nPlease check the import file and try again.";

        // Build HTML version with nice formatting
        $htmlRows = '';
        if (! empty($this->errors)) {
            $htmlRows = '<table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px;">';
            $htmlRows .= '<thead>
                <tr style="background: #fef2f2; border-bottom: 2px solid #fecaca;">
                    <th style="padding: 10px 12px; text-align: left; color: #991b1b; font-weight: 600; width: 80px;">Row</th>
                    <th style="padding: 10px 12px; text-align: left; color: #991b1b; font-weight: 600;">Error</th>
                </tr>
            </thead>';
            $htmlRows .= '<tbody>';
            foreach ($this->errors as $row => $error) {
                $htmlRows .= '<tr style="border-bottom: 1px solid #f1f5f9;">';
                $htmlRows .= '<td style="padding: 8px 12px; font-family: monospace; color: #64748b; vertical-align: top;">R' . $row . '</td>';
                $htmlRows .= '<td style="padding: 8px 12px; color: #334155;">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</td>';
                $htmlRows .= '</tr>';
            }
            $htmlRows .= '</tbody></table>';
        }

        $sourceHtml = '';
        if ($this->source) {
            $sourceLabel = match ($this->source) {
                'validation' => 'Validation Errors',
                'file'       => 'File Error',
                'logic'      => 'Logic Errors',
                default      => ucfirst($this->source),
            };
            $badgeColor = match ($this->source) {
                'validation' => '#f59e0b',
                'file'       => '#ef4444',
                'logic'      => '#f97316',
                default      => '#64748b',
            };
            $sourceHtml = '<p><strong>Source:</strong> <span style="display: inline-block; padding: 2px 10px; background: ' . $badgeColor . '20; color: ' . $badgeColor . '; border-radius: 12px; font-size: 13px; font-weight: 600;">' . $sourceLabel . '</span></p>';
        }

        $html = "<p>Import completed with <strong>{$this->errorCount}</strong> error(s).</p>"
            . $sourceHtml
            . $htmlRows
            . '<p style="margin-top: 20px; padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; color: #991b1b;">'
            . 'Please correct the errors above and re-upload the file.</p>'
            . '<p style="text-align: center; margin-top: 24px;">'
            . '<a href="' . url('/import-export') . '" class="btn">View Import Results</a></p>';

        return [
            'subject' => $subject,
            'body'    => $body,
            'html'    => $html,
        ];
    }
}
