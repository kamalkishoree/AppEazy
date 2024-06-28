import { Platform } from 'react-native';
import { getBundleId } from 'react-native-device-info';

const shortCodes = {
  superApp: '9f5702',
};

const appIds = {
  superApp: Platform.select({
    ios: 'com.superApp.royodispatcher',
    android: 'com.superapp.driver',
  }),
};

export { appIds, shortCodes };
