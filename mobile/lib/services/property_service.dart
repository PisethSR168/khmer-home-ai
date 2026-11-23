import '../constants/api_constants.dart';
import '../models/property_model.dart';
import 'api_service.dart';

class PropertyService {
  final ApiService _api = ApiService();

  Future<List<PropertyModel>> getProperties({
    String? district,
    String? propertyType,
    double? minPrice,
    double? maxPrice,
    int? bedrooms,
    String? search,
  }) async {
    final queryParams = <String, String>{};
    if (district != null && district.isNotEmpty) queryParams['district'] = district;
    if (propertyType != null && propertyType.isNotEmpty) queryParams['property_type'] = propertyType;
    if (minPrice != null) queryParams['min_price'] = minPrice.toString();
    if (maxPrice != null) queryParams['max_price'] = maxPrice.toString();
    if (bedrooms != null) queryParams['bedrooms'] = bedrooms.toString();
    if (search != null && search.isNotEmpty) queryParams['search'] = search;

    final uri = Uri.parse(ApiConstants.properties).replace(queryParameters: queryParams);
    final res = await _api.get(uri.toString());

    final data = res['data']['data'] as List? ?? [];
    return data.map((json) => PropertyModel.fromJson(json)).toList();
  }

  Future<PropertyModel> getPropertyDetail(String idOrSlug) async {
    final res = await _api.get('${ApiConstants.properties}/$idOrSlug');
    return PropertyModel.fromJson(res['data']);
  }

  Future<List<PropertyModel>> getFavorites() async {
    final res = await _api.get(ApiConstants.favorites);
    final list = res['data'] as List? ?? [];
    return list.map((json) => PropertyModel.fromJson(json)).toList();
  }

  Future<bool> toggleFavorite(int propertyId) async {
    final res = await _api.post(ApiConstants.toggleFavorite(propertyId), {});
    return res['data']['is_favorited'] == true;
  }
}
