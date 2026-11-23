import 'package:flutter/material.dart';
import '../models/property_model.dart';
import '../services/property_service.dart';

class PropertyProvider extends ChangeNotifier {
  final PropertyService _propertyService = PropertyService();

  List<PropertyModel> _properties = [];
  List<PropertyModel> _favorites = [];
  bool _isLoading = false;
  String? _errorMessage;

  // Selected filters
  String? selectedDistrict;
  String? selectedPropertyType;
  double? minPrice;
  double? maxPrice;
  int? bedrooms;

  List<PropertyModel> get properties => _properties;
  List<PropertyModel> get favorites => _favorites;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchProperties({String? search}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _properties = await _propertyService.getProperties(
        district: selectedDistrict,
        propertyType: selectedPropertyType,
        minPrice: minPrice,
        maxPrice: maxPrice,
        bedrooms: bedrooms,
        search: search,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchFavorites() async {
    try {
      _favorites = await _propertyService.getFavorites();
      notifyListeners();
    } catch (_) {}
  }

  Future<void> toggleFavorite(PropertyModel property) async {
    final newStatus = !property.isFavorited;
    property.isFavorited = newStatus;

    if (newStatus) {
      if (!_favorites.any((p) => p.id == property.id)) {
        _favorites.add(property);
      }
    } else {
      _favorites.removeWhere((p) => p.id == property.id);
    }
    notifyListeners();

    try {
      final actualStatus = await _propertyService.toggleFavorite(property.id);
      property.isFavorited = actualStatus;
      notifyListeners();
    } catch (_) {
      // Revert if API failed
      property.isFavorited = !newStatus;
      notifyListeners();
    }
  }

  void resetFilters() {
    selectedDistrict = null;
    selectedPropertyType = null;
    minPrice = null;
    maxPrice = null;
    bedrooms = null;
    fetchProperties();
  }
}
