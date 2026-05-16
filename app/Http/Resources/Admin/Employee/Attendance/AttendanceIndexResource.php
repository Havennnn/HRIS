<?php

namespace App\Http\Resources\Admin\Employee\Attendance;

use App\Enums\Status\AttendanceStatus;
use App\Models\AttendanceTag;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tags = $this->resolveAttendanceTags();
        $status = $this->resolveAttendanceStatus($tags);

        return [
            'id' => $this->id,
            'date' => $this->date?->format('M d, Y'),
            'time_in' => $this->time_in?->format('h:i A'),
            'time_out' => $this->time_out?->format('h:i A'),
            'late_minutes' => $this->late_minutes,
            'overtime_minutes' => $this->overtime_minutes,
            'status' => $status?->badge(),
            'status_value' => $status?->value,
            'tags' => $tags->map(fn (AttendanceTag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'value' => $tag->value,
                'badge' => $tag->value?->badge(),
            ])->values(),
            'created_at' => $this->created_at?->format('M d, Y'),
        ];
    }

    /**
     * @return Collection<int, AttendanceTag>
     */
    private function resolveAttendanceTags(): Collection
    {
        return $this->relationLoaded('tags')
            ? $this->tags
            : $this->tags()->get();
    }

    private function resolveAttendanceStatus(Collection $tags): ?AttendanceStatus
    {
        $firstTag = $tags->first();

        if ($firstTag instanceof AttendanceTag && $firstTag->value instanceof AttendanceStatus) {
            return $firstTag->value;
        }

        $rawStatus = $this->status;

        if ($rawStatus instanceof AttendanceStatus) {
            return $rawStatus;
        }

        if (is_numeric($rawStatus)) {
            return AttendanceStatus::tryFrom((int) $rawStatus);
        }

        return null;
    }
}
