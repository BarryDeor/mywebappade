<?php

namespace HRPayroll\TimeAttendance\Entity;

use DateTime;

class AttendanceRecord
{
    private string $id;
    private string $tenantId;
    private string $employeeId;
    private DateTime $clockInTime;
    private ?DateTime $clockOutTime;
    private ?array $clockInLocation;
    private ?array $clockOutLocation;
    private ?string $deviceId;
    private ?float $hoursWorked;
    private ?float $overtimeHours;
    private string $status; // CLOCKED_IN, CLOCKED_OUT, INCOMPLETE
    private ?string $shiftId;
    private array $breaks;
    private array $metadata;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        string $id,
        string $tenantId,
        string $employeeId,
        DateTime $clockInTime,
        ?array $location = null,
        ?string $deviceId = null
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->employeeId = $employeeId;
        $this->clockInTime = $clockInTime;
        $this->clockInLocation = $location;
        $this->deviceId = $deviceId;
        $this->status = 'CLOCKED_IN';
        $this->breaks = [];
        $this->metadata = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function clockOut(DateTime $clockOutTime, ?array $location = null): void
    {
        $this->clockOutTime = $clockOutTime;
        $this->clockOutLocation = $location;
        $this->status = 'CLOCKED_OUT';
        $this->calculateHours();
        $this->updatedAt = new DateTime();
    }

    public function addBreak(DateTime $startTime, DateTime $endTime): void
    {
        $this->breaks[] = [
            'startTime' => $startTime->format('c'),
            'endTime' => $endTime->format('c'),
            'duration' => ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600
        ];
        $this->calculateHours();
        $this->updatedAt = new DateTime();
    }

    private function calculateHours(): void
    {
        if (!$this->clockOutTime) {
            return;
        }

        $totalSeconds = $this->clockOutTime->getTimestamp() - $this->clockInTime->getTimestamp();
        $breakSeconds = array_reduce($this->breaks, function($carry, $break) {
            return $carry + ($break['duration'] * 3600);
        }, 0);

        $this->hoursWorked = ($totalSeconds - $breakSeconds) / 3600;

        // Calculate overtime (assuming 8 hours is standard)
        $this->overtimeHours = max(0, $this->hoursWorked - 8);
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getEmployeeId(): string { return $this->employeeId; }
    public function getStatus(): string { return $this->status; }
    public function getHoursWorked(): ?float { return $this->hoursWorked; }
    public function getOvertimeHours(): ?float { return $this->overtimeHours; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'employeeId' => $this->employeeId,
            'clockInTime' => $this->clockInTime->format('c'),
            'clockOutTime' => $this->clockOutTime?->format('c'),
            'clockInLocation' => $this->clockInLocation,
            'clockOutLocation' => $this->clockOutLocation,
            'deviceId' => $this->deviceId,
            'hoursWorked' => $this->hoursWorked,
            'overtimeHours' => $this->overtimeHours,
            'status' => $this->status,
            'shiftId' => $this->shiftId,
            'breaks' => $this->breaks,
            'createdAt' => $this->createdAt->format('c'),
            'updatedAt' => $this->updatedAt->format('c')
        ];
    }
}
