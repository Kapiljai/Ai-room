# Room AI — Laravel + React + TypeScript

Production-minded take-home implementation for room visualization.

## Stack
- Laravel 11 / PHP 8.2
- Laravel Queue + Redis
- SQLite for local development (PostgreSQL/MySQL ready)
- React 18 + TypeScript + Vite
- Tailwind-free, clean CSS UI
- AI pipeline abstraction: Mock driver by default, HTTP driver for a real CV/GenAI service

## Flow
1. React uploads JPEG/PNG.
2. Laravel validates and creates a UUID job.
3. Laravel dispatches `ProcessRoomJob` to Redis.
4. Worker runs segmentation → object detection → inpainting → furnished generation through `AiPipelineInterface`.
5. Intermediate artifacts are persisted and exposed by API.
6. React polls job status and displays outputs.

## Setup
```bash
cp .env.example .env
composer install
php artisan key:generate
mkdir -p database && touch database/database.sqlite
php artisan migrate
php artisan storage:link
php artisan serve
```

Frontend:
```bash
cd resources/react
npm install
npm run dev
```

Worker:
```bash
php artisan queue:work --tries=3
```

For Redis, set `QUEUE_CONNECTION=redis`. For real inference, set `AI_DRIVER=http` and provide `AI_SERVICE_URL`.

## API
`POST /api/v1/jobs` multipart field `image`.
`GET /api/v1/jobs/{job}` returns status, progress and artifact URLs.
`GET /api/v1/jobs/{job}/artifacts/{artifact}` downloads an artifact.

## Why the AI service is separated
Segmentation, object detection and diffusion/inpainting models are generally Python/GPU-first. Laravel owns authentication, validation, persistence, queueing, retries and API orchestration while the AI driver can call a dedicated inference service without coupling the web application to model runtime.
