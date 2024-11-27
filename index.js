import 'react-native-gesture-handler';
import { AppRegistry } from 'react-native';
import App from './App';
import { name as appName } from './app.json';
console.disableYellowBox = true;
import messaging from '@react-native-firebase/messaging';
import actions from './src/redux/actions';
import { Platform } from 'react-native';
import { navigate } from './src/navigation/NavigationService';
import navigationStrings from './src/navigation/navigationStrings';
import { showhideNotificationModal } from './src/utils/helperFunctions';
// Register background handler
messaging().setBackgroundMessageHandler(async remoteMessage => {
  console.log('Message handled in the background!', remoteMessage);
  const { data, messageId, notification } = remoteMessage;
  let notificationType = data?.type ? data?.type : data?.notificationType;

  if (!!data?.room_id) {
    setTimeout(() => {
      navigate(navigationStrings.CHAT_ROOM, {
        _id: data?.room_id, room_id: data?.room_id_text, ...data,
      });
    }, 5000)
  }
  if (
    Platform.OS == 'android' &&
    notification?.android?.sound == 'notification'
  ) {
    if (notificationType != 'N') {
      if (data?.callback_url) {
        navigate(navigationStrings.ORDERDETAIL, {
          data: {
            item: data?.callback_url,
            fromNotification: true,
          },
        });
      } else {
        console.log(' hey i am here');
        actions.isModalVisibleForAcceptReject({
          isModalVisibleForAcceptReject:showhideNotificationModal(notificationType),
          notificationData: remoteMessage,
        });
      }
    }
  }
});
AppRegistry.registerComponent(appName, () => App);