import '../constants/api_constants.dart';
import '../models/user_model.dart';
import 'api_service.dart';

class AuthService {
  final ApiService _api = ApiService();

  Future<UserModel> login(String email, String password) async {
    final res = await _api.post(ApiConstants.login, {
      'email': email,
      'password': password,
    });

    final token = res['data']['token'];
    await _api.setToken(token);

    return UserModel.fromJson(res['data']['user']);
  }

  Future<UserModel> register({
    required String name,
    required String email,
    required String password,
    String? phone,
    String role = 'renter',
    String? businessName,
  }) async {
    final res = await _api.post(ApiConstants.register, {
      'name': name,
      'email': email,
      'password': password,
      'phone': phone,
      'role': role,
      'business_name': businessName,
    });

    final token = res['data']['token'];
    await _api.setToken(token);

    return UserModel.fromJson(res['data']['user']);
  }

  Future<UserModel?> getCurrentUser() async {
    if (!_api.isAuthenticated) return null;
    try {
      final res = await _api.get(ApiConstants.user);
      return UserModel.fromJson(res['data']);
    } catch (_) {
      return null;
    }
  }

  Future<void> logout() async {
    if (_api.isAuthenticated) {
      try {
        await _api.post(ApiConstants.logout, {});
      } catch (_) {}
    }
    await _api.clearToken();
  }
}
