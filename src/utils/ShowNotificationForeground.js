import messaging from '@react-native-firebase/messaging';
import { useEffect } from 'react';
import { Platform } from 'react-native';
// import PushNotification, { Importance } from 'react-native-push-notification';
import notifee, { AndroidColor, AndroidImportance, EventType } from '@notifee/react-native';
import { navigate } from '../navigation/NavigationService';
import navigationStrings from '../navigation/navigationStrings';
import actions from '../redux/actions';
import { showhideNotificationModal } from './helperFunctions';

const ShowNotificationForeground = props => {

  useEffect(() => {
    return notifee.onForegroundEvent(({ type, detail }) => {
      let notidata = detail?.notification?.data || {}
      let clickActionUrl = detail?.pressAction
      let clickActionUrls = detail?.notification?.data?.click_action || null;
      let remoteMessage = detail?.notification
      console.log(clickActionUrls,"clickActionUrls>><");
      console.log(detail,"detail?.notification");
      // let clickActionUrls = "Vendor/El Rinconcito Colombiano/581";
      // let clickActionUrls = "https://testfcm.com/";
      switch (type) {
        case EventType.DISMISSED:
          console.log('User dismissed notification', detail.notification);
          break;
        case EventType.PRESS:
          console.log(notidata, 'User pressed notification', detail);
          if (!!notidata?.room_id && !!clickActionUrl) {
            setTimeout(() => {
              navigate(navigationStrings.CHAT_ROOM, {
                _id: notidata?.room_id, room_id: notidata?.room_id_text, ...notidata,
              });
            }, 400)
          }
        
          break;
      }
    });
  }, []);

  useEffect(() => {
    const unsubscribe = messaging().onMessage(async remoteMessage => {
      console.log('remote message foreground', remoteMessage);
      const { data, messageId, notification } = remoteMessage;
      let notificationType = data?.type || data?.notificationType || 'AR';

      let displayNotificationData = {}

      if (Platform.OS == "ios") {
        displayNotificationData = {
          title: notification?.title || notificationType || '',
          body: data?.message || notification?.body || '',
          ios: {

            sound: notification?.sound == 'notification.mp3' || notification?.sound == 'notification.wav' || notification?.sound == 'notification'
              ? 'notification.mp3'
              : 'default',
          },
          data: { ...data },
        };
      }
      else {
        if (notification?.android?.sound == "notification") {

          var customSoundChannelId = await notifee.createChannel({
            id: notification?.android?.channelId || "Royo-Delivery",
            name: 'Default Channel',
            vibration: true,
            lightColor: AndroidColor.YELLOW,
            sound: notification?.android?.sound || "notification",
            importance: AndroidImportance.HIGH,
          });
        }
        else {
          var channelId = await notifee.createChannel({
            id: 'default',
            name: 'Default Channel',
            vibration: true,
            lightColor: AndroidColor.YELLOW,
            sound: 'default',
            importance: AndroidImportance.HIGH,
          });

        }
        displayNotificationData = {
          title: notification?.title || notificationType || '',
          body: data?.message || notification?.body || '',
          android: {
            sound: notification?.android?.sound == 'notification'
              ? 'notification'
              : 'default',
            channelId: notification?.android?.sound == "notification" ? customSoundChannelId : channelId,
            pressAction: {
              id: 'default',
            },
            importance: AndroidImportance.HIGH,

          },
          data: { ...data },
        };
      }

      await notifee.displayNotification(displayNotificationData);
      if (
        Platform.OS == 'android' &&
        notification?.android?.sound == 'notification'
      ) {
        if (data && notificationType && notificationType != 'N') {
          actions.isModalVisibleForAcceptReject({
            isModalVisibleForAcceptReject: showhideNotificationModal(notificationType),
            notificationData: remoteMessage,
          });
        }
        if (data?.callback_url != '' && data?.callback_url != null) {
          navigate(navigationStrings.ORDERDETAIL, {
            data: { item: data?.callback_url, fromNotification: true },
          });
        }
      }
      if (Platform.OS == 'ios' && (notification?.sound == 'notification.mp3' || notification?.sound == 'notification.wav' ||  notification?.sound == 'notification')) {

        if (data && notificationType && notificationType != 'N') {
          actions.isModalVisibleForAcceptReject({
            isModalVisibleForAcceptReject: showhideNotificationModal(notificationType),
            notificationData: remoteMessage,
          });
        }
        if (data?.callback_url != '' && data?.callback_url != null) {
          navigate(navigationStrings.ORDERDETAIL, {
            data: { item: data?.callback_url, fromNotification: true },
          });
        }
      }
    });
    return unsubscribe;
  }, []);
  return null;
};


export default ShowNotificationForeground;