# Khmer Home AI - Flutter Mobile Application

Modern Android and iOS mobile application for **Khmer Home AI** (គេហដ្ឋានខ្មែរ អេអាយ), communicating directly with the centralized Laravel REST API.

## Features Included

- **Authentication**: Register as Renter or Landlord, Sign in with Sanctum Bearer tokens, Profile management.
- **Room Explorer**: Browse verified rooms, apartments, and studios in Phnom Penh (Boeng Keng Kang, Tuol Kouk, Chamkar Mon, Daun Penh, Sen Sok).
- **Search & Filters**: Search by keyword, district, property type, price range, and bedrooms.
- **Detailed Room Views**: High-resolution image galleries, transparent utility rates breakdown (EDC Electricity \$/kWh, Water \$/m³), amenities badges, and landlord profile cards.
- **Favorites**: Save and manage favorite rooms with real-time sync to the Laravel backend.
- **Viewing Requests**: Schedule viewing appointments with dates, time slots, contact numbers, and custom notes.
- **24/7 AI Rental Assistant**: Conversational AI concierge providing transparent rental advice and real-time room recommendations grounded in the database.

## Architecture

- **Framework**: Flutter (Dart >= 3.0.0)
- **State Management**: Provider
- **Networking**: `http` with `ApiService` token interceptor
- **Storage**: `shared_preferences`
- **Backend**: Laravel REST API (`/api/v1/`)

## Connecting to Local Backend

The backend base URL is configured in `lib/constants/api_constants.dart`:

- **Android Emulator**: `http://10.0.2.2:8000/api/v1`
- **iOS Simulator / Web / Real Device**: `http://localhost:8000/api/v1` or your computer's local IP address (e.g. `http://192.168.1.100:8000/api/v1`)

## Running the App

```bash
cd mobile
flutter pub get
flutter run
```
