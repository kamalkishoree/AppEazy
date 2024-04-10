import {Platform} from 'react-native';
import {getBundleId} from 'react-native-device-info';

const shortCodes = {
  superApp: '041795',
};

const appIds = {
 
  superApp: Platform.select({
    ios: 'com.superapp.royoorder',
    android: 'com.superapp.royoorder',
  }),
 
};

const socialKeys = {
  TWITTER_COMSUMER_KEY: 'R66DHARfuoYAPowApUxNxwbPi',
  TWITTER_CONSUMER_SECRET: 'itcicJ7fUV3b73B8V05GEDBo4tzxGox2Si2q0BCk5pue327k15',
};

export {appIds, socialKeys, shortCodes};
