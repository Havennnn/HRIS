# HRIS (Human Resource Information System)

A Laravel 12 + Vue 3 application for managing human resources, built on top of **PiaCore** - a CRUD admin framework. The system includes employee management, attendance tracking, leave **requests**, payroll, performance reviews, and recruitment.

## Tech Stack

| Component      | Technology                                  |
|----------------|---------------------------------------------|
| Backend        | Laravel 12 (PHP 8.2+)                       |
| Frontend       | Vue 3 + Inertia.js + TypeScript            |
| Styling        | Tailwind CSS 4 + Reka UI                    |
| CRUD Framework | PiaCore (`latsmarbls/piacore`)              |
| Authentication | Laravel Fortify (via PiaCore)              |
| Code Quality   | Pint (PHP), ESLint + Prettier (JS/TS)      |

---

## PiaCore Architecture

This project follows the **PiaCore** pattern for CRUD operations. All development should follow this approach for consistency and faster development.

### Flow Diagram

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Controller │───▶│   Options   │────▶│   Action    │───▶│   Service   │────▶│    Model    │
│   (Thin)    │     │    (DTO)    │     │ (Invokable) │     │  (Business) │     │  (Eloquent) │
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
       │                                                                                │
       │                                                                                │
       └─────────────────────────────────── Response ◄────────────────────────────────────┘
```

### Controller Pattern

Controllers should extend [`PiaCore\Http\Controllers\ResourceController`](vendor/latsmarbls/piacore/src/Http/Controllers/ResourceController.php):

```php
final class EmployeeController extends ResourceController
{
    protected string  $modelClass    = Employee::class;
    protected ?string $serviceClass  = EmployeeService::class;
    protected ?string $routeBase    = 'employees';
    protected ?string $viewBase     = 'Admin/Employees';

    public function index(Request $request, ListAction $action)
    {
        return $action($this->listOptions(
            request         : $request,
            resource        : EmployeeIndexResource::class,
            additionalProps : [
                'positions' => Position::options(),
                'statuses' => EmployeeStatus::options(),
            ]
        ));
    }

    // ... create, edit, store, update, destroy, restore methods
}
```

### Service Pattern

Services implement PiaCore contracts:

```php
class EmployeeService implements ListsRecords
{
    public function list(Model|string|Relation $model, Request $request): array
    {
        return [
            'baseQuery' => fn (Builder $query) => $query->with(['position', 'position.department']),
            'tabs'      => [
                'default'  => ['countKey' => 'defaultCount'],
                'archived' => ['countKey' => 'archivedCount', 'scope' => fn ($q) => $q->onlyTrashed()],
            ],
            'filters'   => [
                'position' => fn ($query, $value) => $query->whereIn('position_id', $value),
                'status'   => fn ($query, $value) => $query->whereIn('status', $value),
            ],
            'sorts'     => [
                'name'   => 'first_name',
                'created' => 'created_at',
            ],
            'range'     => [
                'created' => 'created_at',
            ],
        ];
    }
}
```

### Available PiaCore Actions

| Action           | Purpose                              |
|------------------|--------------------------------------|
| `ListAction`     | Display listing with filters/tabs    |
| `ShowAction`     | Show single record details           |
| `CreateAction`   | Show create form                     |
| `StoreAction`    | Store new record                     |
| `EditAction`     | Show edit form                       |
| `UpdateAction`   | Update existing record               |
| `DeleteAction`   | Soft delete record                   |
| `RestoreAction`  | Restore soft-deleted record          |

### Available PiaCore Contracts

| Contract           | Purpose                              |
|--------------------|--------------------------------------|
| `ListsRecords`     | List configuration                   |
| `ShowsRecords`     | Show single record                   |
| `CreateRecords`    | Store new record                     |
| `EditRecords`      | Edit view data                        |
| `UpdateRecords`     | Update existing record               |
| `DeletesRecords`    | Delete record                         |
| `RestoresRecords`   | Restore record                        |

---

## Aggregate Sync Pattern

This is the **preferred style** for store and update operations in PiaCore. The service becomes the aggregate manager for the entity and its relations.

### Flow

```
Request
  ↓
Controller (thin — just passes options)
  ↓
StoreAction / UpdateAction
  ↓
Service->store() / Service->update()
  ↓
DB::transaction {
  1. extractModelData()     → prepare model attributes + handle file uploads
  2. Model::create/update   → persist the main entity
  3. syncRelations()        → sync all related entities from the same payload
}
```

### Service Structure

```php
class ProductService implements ListsRecords, StoresRecords, UpdatesRecords
{
    // ─── Query ─────────────────────────────────────
    public function list(...): array { }

    // ─── Write ─────────────────────────────────────
    public function store(string $modelClass, array $payload, ?FormRequest $request = null): Model { }
    public function update(Model $record, array $payload, ?FormRequest $request = null): Model { }

    // ─── Data Extraction ───────────────────────────
    protected function extractModelData(array $data, ?FormRequest $request = null): array { }

    // ─── Relation Sync ─────────────────────────────
    protected function syncMedia(Model $record, array $items): void { }
    protected function syncSeo(Model $record, array $data): void { }
}
```

### Extract Model Data

This method separates model attributes from relation data and handles file uploads:

```php
protected function extractModelData(array $data, ?FormRequest $request = null): array
{
    // Remove relation keys — they'll be synced separately
    unset($data['media'], $data['seo'], $data['links']);

    // Handle file uploads → convert to foreign key IDs
    if ($request?->hasFile('image.file')) {
        $data['image_id'] = FileUploader::upload(
            $request->file('image.file'), 'products'
        )->id;
    }
    unset($data['image']);

    return $data;
}
```

### Store Method

```php
public function store(string $modelClass, array $payload, ?FormRequest $request = null): Model
{
    return DB::transaction(function () use ($modelClass, $payload, $request) {
        $record = $modelClass::query()->create(
            $this->extractModelData($payload, $request)
        );

        $this->syncMedia($record, $payload['media'] ?? []);
        $this->syncSeo($record, $payload['seo'] ?? []);

        return $record;
    });
}
```

### Update Method

```php
public function update(Model $record, array $payload, ?FormRequest $request = null): Model
{
    return DB::transaction(function () use ($record, $payload, $request) {
        $record->update(
            $this->extractModelData($payload, $request)
        );

        $this->syncMedia($record, $payload['media'] ?? []);
        $this->syncSeo($record, $payload['seo'] ?? []);

        return $record->fresh();
    });
}
```

### Sync Methods

Each sync method is a small, focused function:

```php
protected function syncMedia(Model $record, array $items): void
{
    if (empty($items)) {
        return;
    }

    $existingIds = collect($items)->pluck('id')->filter()->all();

    // Remove media not in the new list
    $record->media()->whereNotIn('id', $existingIds)->delete();

    // Upsert each item
    foreach ($items as $item) {
        $record->media()->updateOrCreate(
            ['id' => $item['id'] ?? null],
            ['url' => $item['url'], 'sort_order' => $item['sort_order'] ?? 0]
        );
    }
}
```

---

## Model Traits

PiaCore provides several traits for models in `PiaCore\Models\Concerns\`:

### HasActivityLogs
Tracks all admin actions automatically using Spatie Laravel Activity Log.

```php
use PiaCore\Models\Concerns\HasActivityLogs;

class Employee extends Model
{
    use HasActivityLogs;
    // ...
}
```

### HasArchives
Enables soft deletes with archive tracking.

```php
use PiaCore\Models\Concerns\HasArchives;

class Employee extends Model
{
    use HasArchives;
    // ...
}
```

### HasUploadedFiles
Provides polymorphic file relationships.

```php
use PiaCore\Models\Concerns\HasUploadedFiles;

class Employee extends Model
{
    use HasUploadedFiles;
    
    public function avatar()
    {
        return $this->uploadedFile('avatar');
    }
}
```

### HasPermissions
Provides role-based permissions.

```php
use PiaCore\Models\Concerns\HasPermissions;

class Employee extends Model
{
    use HasPermissions;
    // ...
}
```

---

## Enum Pattern

Enums should use PiaCore traits for UI integration:

```php
use PiaCore\Concerns\HasOptions;
use PiaCore\Enums\Concerns\HasMeta;

enum EmployeeStatus: int
{
    use HasMeta;
    use HasOptions;

    case ACTIVE   = 1;
    case INACTIVE = 2;

    protected static function metaMap(): array
    {
        return [
            self::ACTIVE->value   => [
                'label' => 'Active',   
                'variant' => 'badge-active'
            ],
            self::INACTIVE->value => [
                'label' => 'Inactive', 
                'variant' => 'badge-inactive'
            ],
        ];
    }
}
```

**Available methods:**

| Method                       | Description                           |
|------------------------------|---------------------------------------|
| `EmployeeStatus::options()`  | Returns array for dropdowns           |
| `EmployeeStatus::optionsForSelect()` | Returns array for form selects |
| `$status->label()`           | Returns the label                     |
| `$status->badge()`          | Returns badge array with label/variant |
| `$status->value`            | Returns the integer value             |
| `$status->toArray()`        | Returns full meta array              |

---

## Permission System

PiaCore uses config-based permissions in `config/piapermission.php`. No database permission tables required.

### Permission Pattern

```php
// config/piapermission.php
return [
    'can-list-employees'   => [
        'label' => 'List Employees',   
        'roles' => [
            AdminRole::SUPER_ADMIN
        ]
    ],
    'can-create-employee'  => [
        'label' => 'Create Employee', 
        'roles' => [
            AdminRole::SUPER_ADMIN
        ]
    ],
    'can-update-employee'  => [
        'label' => 'Update Employee', 
        'roles' => [
            AdminRole::SUPER_ADMIN
        ]
    ],
    'can-archive-employee' => [
        'label' => 'Archive Employee', 
        'roles' => [
            AdminRole::SUPER_ADMIN
        ]
    ],
    'can-restore-employee' => [
        'label' => 'Restore Employee', 
        'roles' => [
            AdminRole::SUPER_ADMIN
        ]
    ],
    // Available actions: list, view, create, update, archive, restore
];
```

### Checking Permissions

```php
use PiaCore\Models\Admin;

$admin->hasPermission('can-list-employees');
$admin->can('can-create-employee');
```

---

## ListOptions Configuration

The `list()` method in Service should return an array with these keys:

### baseQuery
Callable to modify the base query with relationships:

```php
'baseQuery' => fn (Builder $query) => $query->with(['position', 'department']),
```

### tabs
Define tabs for filtering records:

```php
'tabs' => [
    'default'  => ['countKey' => 'defaultCount'],
    'archived' => ['countKey' => 'archivedCount', 'scope' => fn ($q) => $q->onlyTrashed()],
],
```

### filters
Define filters for the list:

```php
'filters' => [
    'position'  => fn ($query, $value) => $query->whereIn('position_id', $value),
    'status'   => fn ($query, $value) => $query->whereIn('status', $value),
    'department' => ['column' => 'department_id', fn ($query, $value) => $query->whereIn('department_id', $value)],
],
```

### sorts
Define sortable columns:

```php
'sorts' => [
    'name'    => 'first_name',           // Maps 'name' sort param to 'first_name' column
    'created' => 'created_at',
],
```

### range
Define date range filters:

```php
'range' => [
    'created' => 'created_at',        // Enables date range filter on 'created_at'
],
```

---

## File Upload Handling

Use the `FileUploader` service for handling file uploads:

```php
use PiaCore\Services\FileUploader;

// In Service store/update method:
if ($request->hasFile('image.file')) {
    $data['image_id'] = FileUploader::upload(
        $request->file('image.file'),
        $request->user('admin'),
        ['directory' => 'employees']
    )->id;
}
```

---

## Project Structure

```
app/
├── Enums/
│   ├── Status/           # Status enums (EmployeeStatus, AttendanceStatus, etc.)
│   └── Type/             # Type enums (EmployeeType, ShiftType, etc.)
├── Http/
│   ├── Controllers/Admin/
│   │   └── [Module]/
│   │       └── [Module]Controller.php
│   ├── Requests/Admin/
│   │   └── [Module]/
│   │       └── [Module]Request.php
│   └── Resources/Admin/
│       └── [Module]/
│           ├── [Module]IndexResource.php
│           └── [Module]EditResource.php
├── Models/
│   └── Employee.php, Department.php, Position.php, etc.
├── Services/
│   └── Admin/
│       └── [Module]/
│           └── [Module]Service.php
└── Actions/               # Laravel Fortify actions
```

---

## Code Style

### PHP (Laravel + PiaCore)

- Use Laravel preset for Pint: `"preset": "laravel"`
- Follow PiaCore controller pattern
- Use PHP 8.2+ features (attributes, enums, readonly properties)
- Use Form Requests for validation
- Use enums with HasMeta/HasOptions traits

### Vue/TypeScript

- Use TypeScript with `type-imports` style
- Follow ESLint configuration
- Use Composition API with `<script setup>` syntax
- Inertia pages go in `resources/js/Pages/Admin/[Module]/`

### CSS/Tailwind

- Use Tailwind CSS 4 syntax
- Follow Prettier formatting with `prettier-plugin-tailwindcss`

---

## Naming Conventions

| Type              | Convention                                |
|-------------------|-------------------------------------------|
| Models            | Singular, PascalCase (e.g., `Employee`)  |
| Controllers       | `[Module]Controller.php` extends ResourceController |
| Services          | `[Module]Service.php`                     |
| Enums             | `[Module]Status` or `[Module]Type`       |
| Form Requests     | `[Module]Request.php`                     |
| API Resources     | `[Module]IndexResource.php`, `[Module]EditResource.php` |
| Routes            | RESTful, auto-configured via PiaCore     |
| Vue Pages         | `resources/js/Pages/Admin/[Module]/Index.vue` |

---

## Key Features

### Employee Management
- CRUD operations for employees
- Documents, contacts, devices, and tools tracking
- Department and position assignments

### Attendance
- Daily attendance tracking via `Attendance` and `AttendanceLog` models
- Shift management with `Shift` model
- Status-based attendance (present, and late)

### Leave Management
- Leave request submission and approval
- Status workflow via `LeaveRequestStatus` enum

### Payroll
- Payroll processing with `Payroll` model
- Adjustments (bonuses, deductions) via `PayrollAdjustment`

### Performance
- KPI definition via `Kpi` model
- Performance reviews with `PerformanceReview`
- Review scores and feedback

### Recruitment
- Job applications via `Application` model
- Application details and interviews
- Status tracking

---

## Security

- Never commit API keys or secrets to `.env`
- Use environment variables for sensitive data
- Validate all user inputs via Form Requests
- Use PiaCore's built-in permission system (`config/piapermission.php`)
- Models should use `HasActivityLogs` trait for audit trails

---

## Useful Commands

```bash
# Setup
composer setup

# Development
composer dev

# Linting
composer lint          # Auto-fix PHP
composer lint:check    # Check PHP
npm run lint          # Auto-fix JS/TS
npm run format        # Auto-format

# Testing
composer test
```

---

## Configuration Files

| File                   | Purpose                                        |
|------------------------|-----------------------------------------------|
| `config/piacore.php`   | Route prefix, Inertia prefix, search          |
| `config/piapermission.php` | Permission/role configuration          |

---

## Reference Documentation

- [PiaCore README](vendor/latsmarbls/piacore/README.md) - Full PiaCore documentation
- [Aggregate Sync Pattern](vendor/latsmarbls/piacore/docs/aggregate-sync-pattern.md) - Store & Update strategy
- [vendor/latsmarbls/piacore/src/](vendor/latsmarbls/piacore/src/) - Source code for contracts, actions, and base classes