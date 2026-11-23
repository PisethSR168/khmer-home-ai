class ApiConstants {
  // Use 10.0.2.2 for Android Emulator, 127.0.0.1/localhost for iOS simulator, or your local machine IP
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1';

  // Alternative for iOS simulator or local network
  static const String localBaseUrl = 'http://localhost:8000/api/v1';

  // Auth endpoints
  static const String register = '$baseUrl/auth/register';
  static const String login = '$baseUrl/auth/login';
  static const String logout = '$baseUrl/auth/logout';
  static const String user = '$baseUrl/auth/user';
  static const String updateProfile = '$baseUrl/auth/profile';

  // Property endpoints
  static const String properties = '$baseUrl/properties';
  static const String amenities = '$baseUrl/amenities';
  static const String favorites = '$baseUrl/favorites';
  static String toggleFavorite(int id) => '$baseUrl/favorites/$id/toggle';

  // Viewing requests
  static const String viewingRequests = '$baseUrl/viewing-requests';
  static String updateViewingStatus(int id) => '$baseUrl/viewing-requests/$id/status';

  // AI endpoints
  static const String aiSmartSearch = '$baseUrl/ai/smart-search';
  static const String aiRecommendations = '$baseUrl/ai/recommendations';
  static const String aiRentalAssistant = '$baseUrl/ai/rental-assistant';
  static const String aiGenerateDescription = '$baseUrl/ai/generate-description';
  static const String aiCheckCompleteness = '$baseUrl/ai/check-completeness';

  // Notifications
  static const String notifications = '$baseUrl/notifications';
  static String markNotificationRead(int id) => '$baseUrl/notifications/$id/read';
}
