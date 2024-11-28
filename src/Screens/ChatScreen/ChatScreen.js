import React, { useState, useCallback, useEffect, useRef } from 'react';
import {
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
  Image,
  Platform,
  ImageBackground,
  PermissionsAndroid,
} from 'react-native';
import { GiftedChat, InputToolbar, Send } from 'react-native-gifted-chat';
import socketServices from '../../utils/scoketService';
import { useSelector } from 'react-redux';
import imagePath from '../../constants/imagePath';
import Header from '../../Components/Header';
import colors from '../../styles/colors';
import WrapperContainer from '../../Components/WrapperContainer';
import actions from '../../redux/actions';
import { getImageUrl } from '../../utils/helperFunctions';
import { cameraImgVideoHandler } from '../../utils/commonFunction';
import {
  height,
  moderateScale,
  moderateScaleVertical,
  textScale,
  width,
} from '../../styles/responsiveSize';
import FastImage from 'react-native-fast-image';
import moment from 'moment';
import _,{ cloneDeep, isEmpty }from 'lodash';
import CircularImages from '../../Components/CircularImages';
import Modal from 'react-native-modal';
import { ScrollView } from 'react-native-gesture-handler';
import fontFamily from '../../styles/fontFamily';
import { useFocusEffect, useNavigation } from '@react-navigation/native';
import navigationStrings from '../../navigation/navigationStrings';
import ChatMedia from '../../Components/ChatMedia';
import ButtonImage from '../../Components/ImageComp';
import ActionSheet from 'react-native-actionsheet';
import strings from '../../constants/lang';
import { androidCameraPermission } from '../../utils/permissions';
import DocumentPicker from 'react-native-document-picker';
import { createThumbnail } from 'react-native-create-thumbnail';
import 'react-native-get-random-values';
import { v4 as uuidv4 } from 'uuid';
import { getAppCode } from '../ShortCode/getAppCode';

export default function ChatScreen({ route }) {
  const navigation = useNavigation()
  const paramData = route.params.data;
  const clientInfo = useSelector(state => state?.initBoot?.clientInfo);
  const defaultLanguagae = useSelector(
    state => state?.initBoot?.defaultLanguage,
  );
  const {
    appData,
  } = useSelector(state => state.initBoot);
  let actionSheet = useRef();

  const userData = useSelector(state => state?.auth?.userData);

  console.log('paramDataparamData', paramData);

  const styles = stylesFun({});

  const [messages, setMessages] = useState([]);
  const [state, setState] = useState({
    showParticipant: false,
    isLoading: false,
    roomUsers: [],
    allRoomUsersAppartFromAgent: [],
    allAgentIds: [],
    allAgentIds: [],
  });
  const {
    isLoading,
    roomUsers,
    showParticipant,
    allRoomUsersAppartFromAgent,
    allAgentIds,
  } = state;

  const updateState = data => setState(state => ({ ...state, ...data }));

  useEffect(() => {
    socketServices.on('new-message', data => {
      console.log(data, 'data to be emitted in chat screen');
      if (
        paramData?.room_id == data?.message?.roomData?.room_id &&
        paramData?.room_name == data?.message?.roomData?.room_name
      ) {
        setMessages(previousMessages =>
          GiftedChat.append(previousMessages, {
            ...data.message.chatData,
            user: { _id: 0 },
          }),
        );
      }
      // fetchAllMessages()
      fetchAllRoomUser();
    });
    return () => {
      socketServices.removeListener('new-message');
      socketServices.removeListener('save-message');
    };
  }, []);

  console.log('all messages', messages);

  useEffect(() => {
    updateState({ isLoading: true });
    fetchAllRoomUser();
    fetchAllMessages();
  }, []);

  const fetchAllMessages = useCallback(async () => {
    try {
      const apiData = `/${paramData?._id}`;
      const res = await actions.getAllMessages(apiData, {});
      console.log('fetchAllMessages res', res);
      updateState({ isLoading: false });
      if (!!res) {
        setMessages(res.reverse());
      }
    } catch (error) {
      console.log('error raised in fetchAllMessages api', error);
      updateState({ isLoading: false });
    }
  }, []);

  const fetchAllRoomUser = async () => {
    try {
      const apiData = `/${paramData?._id}`;
      const res = await actions.getAllRoomUser(
        apiData,
        {},
        {
          client: clientInfo?.database_name,
          language: defaultLanguagae?.value ? defaultLanguagae?.value : 'en',
        },
      );
      console.log(res, 'resresresres');
      if (!!res?.userData) {
        const allRoomUsersAppartFromAgent = res?.userData.filter(function (el) {
          return el.user_type != 'agent';
        });
        const allAgentIds = res?.userData.filter(function (el) {
          return el.user_type == 'agent';
        });

        updateState({
          allRoomUsersAppartFromAgent: allRoomUsersAppartFromAgent,
          allAgentIds: allAgentIds,
          roomUsers: res?.userData,
        });
      }
    } catch (error) {
      console.log('error raised in fetchAllRoomUser api', error);
    }
  };

  const onSend = useCallback(
    async (messages = []) => {
      if (String(messages[0].text).trim().length < 1) {
        return;
      }
      console.log(userData,"dfkjksjdfk");
      
      if (
        String(messages[0].text).trim().length < 1 ||
        messages[0]?.mediaUrl == ''
      ) {
        return;
      }

      let userImage = !!userData?.source
        ? getImageUrl(
          userData?.source?.proxy_url,
          userData?.source?.image_path,
          '200/200',
        )
        : null;
        var apiData
      try {
         apiData = {
          room_id: paramData?._id,
          message: messages[0].text,
          user_type: 'agent',
          to_message: 'to_user',
          from_message: 'from_agent',
          user_id: userData?.id || '',
          email: userData?.email || '',
          username: userData?.name || '',
          phone_num: `${userData.phone_number}`,
          display_image: userData?.image_url,
          // sub_domain: clientInfo?.custom_domain,
          //'room_name' =>$data->name,
          chat_type: 'agent_to_user',
        };
        if (!!messages[0]?.isMedia) {
          apiData = {
            ...apiData,
            is_media: true,
            mediaUrl: messages[0]?.mediaUrl,
            thumbnailUrl: messages[0]?.mediaUrl,
            mediaType: messages[0]?.type,
          };
        }
        console.log('sending api data111111', apiData);
        const res = await actions.sendMessage(apiData, {
          client: clientInfo?.database_name,
          language: defaultLanguagae?.value ? defaultLanguagae?.value : 'en',
        });
        console.log('on send message res', res);
        socketServices.emit('save-message', res);

        await sendToUserNotification(paramData?._id, messages[0].text);
      } catch (error) {
        console.log('error raised in sendMessage api', error);
      }
    },
    [allRoomUsersAppartFromAgent, allAgentIds],
  );

  const sendToUserNotification = async (id, text) => {
    let apiData = {
      user_ids:
        allRoomUsersAppartFromAgent.length == 0
          ? [{ auth_user_id: paramData?.order_user_id }]
          : allRoomUsersAppartFromAgent,
      roomId: id,
      roomIdText: paramData?.room_id,
      text_message: text,
      chat_type: paramData?.type,
      order_number: paramData?.room_id,
      all_agentids: allAgentIds,
      order_vendor_id: paramData?.order_vendor_id,
      username: userData?.name,
      vendor_id: paramData?.vendor_id,
      auth_id: userData?.id,
      web: false,
      from: 'from_dispatcher',
      order_id: paramData?.order_id,
    };
    console.log(
      allRoomUsersAppartFromAgent,
      'sending api data>>>>> notification',
      apiData,
    );

    try {
      const res = await actions.sendNotification(apiData, {
        client: clientInfo?.database_name,
        language: defaultLanguagae?.value ? defaultLanguagae?.value : 'en',
      });
      console.log('res sendNotification', res);
    } catch (error) {
      console.log('error raised in sendToUserNotification api', error);
    }
  };

  const appendMediaPreview = (media, thumbnail = '') => { // to set preview/thumbnail of image/video/document while uploading video
    let allMessages = cloneDeep(messages);
    let newMsg = {
      ...allMessages[0], // Copy properties from the first item
      mediaType: media?.mime,
      is_media: true,
      mediaUrl: media?.path,
      _id: allMessages[0]?._id + uuidv4(),
      isLoading: true,
      auth_user_id: userData?.id,
      name: media?.modificationDate,
    };
    if (media?.mime == 'video/mp4') {
      newMsg.thumbnailUrl = thumbnail;
    }
    allMessages.unshift(newMsg);
    console.log(newMsg,'newMsgnewMsg');
    
    setMessages(allMessages);
  };

  const uploadMedia = (fileRes = [], fileName = '') => { // To upload media filed to S3 server
    console.log(fileRes, '<====fileRes');
    if (!isEmpty(fileRes)) {
      let encodedData = encodeURIComponent(
        `uploads/${userData?.id}/${paramData?._id}/${fileName}`,
      ); //encoded media data for AWS-S3
      console.log(encodedData, '<====encodedData');

      actions
        .uploadMediaS3(
          encodedData,
          {},
          {
            // API to get presigned URL from S3
            code: getAppCode(),
            // currency: currencies?.primary_currency?.id,
            language: defaultLanguagae?.value? defaultLanguagae?.value : 'en' ,
            client: clientInfo?.database_name
          },
        )
        .then(async res => {
          const response = await fetch(fileRes.path);
          console.log(response,'responseresponse');
          const blob = await response.blob(); // converts media to blob
          console.log(blob,'blobblob',res?.url);
          fetch(res?.url, {
            // API to upload presigned URL to AWS directly
            method: 'PUT',
            body: blob,
          })
            .then(data => {
              console.log(data, '<===afterputS3');
              const hostname = data?.url.match(/^(https?:\/\/)([^:/\n]+)/)[0];
              let mediaUrl = hostname + `/${encodedData}`;

              onSend([
                {
                  mediaUrl: mediaUrl,
                  type: fileRes?.mime || fileRes?.type,
                  isMedia: true,
                },
              ]); // to send media info in user chat
            })
            .catch(err => {
              console.log(err,"err>>>>>>>>>>>> inner ");
              
              showError('Something went wrong inner');
            });
        })
        .catch(err => {
          console.log(err,"err>>>>>>>>>>>> outer ");
          showError('Something went wrong outer ');
        });
    }
  };

  const cameraHandle = async (index = 0) => {
    if (index === 2) { // to open device's document gallary
      try {
        const granted = await PermissionsAndroid.requestMultiple([
          PermissionsAndroid.PERMISSIONS.READ_EXTERNAL_STORAGE,
          PermissionsAndroid.PERMISSIONS.WRITE_EXTERNAL_STORAGE,
        ]);
        if (
          granted['android.permission.READ_EXTERNAL_STORAGE'] ===
          PermissionsAndroid.RESULTS.GRANTED &&
          granted['android.permission.WRITE_EXTERNAL_STORAGE'] ===
          PermissionsAndroid.RESULTS.GRANTED
        ) {
          try {
            const res = await DocumentPicker.pick({
              type: [
                DocumentPicker.types.pdf,
                DocumentPicker.types.zip,
                DocumentPicker.types.doc,
                DocumentPicker.types.docx,
                DocumentPicker.types.ppt,
                DocumentPicker.types.pptx,
                DocumentPicker.types.xls,
                DocumentPicker.types.xlsx,
              ],
            });

            if (!!res) {
              let fileObj = {
                path: res[0]?.uri,
                mime: 'docs',
                name: res[0]?.name,
              };
              uploadMedia(fileObj, (name = res[0]?.name));
              // appendMediaPreview(fileObj);
            }
          } catch (err) {
            if (DocumentPicker.isCancel(err)) {
              // User cancelled the picker, exit any dialogs or menus and move on
            } else {
              throw err;
            }
          }
        } else {
          // Permission denied, handle accordingly
        }
      } catch (err) {
        console.warn(err);
      }
    }

    const permissionStatus = await androidCameraPermission();

    if (permissionStatus) { // to open device's image / video gallary
      cameraImgVideoHandler(index, {
        mediaType: 'any',
      })
        .then(async res => {
          if (!!res?.path) {
            console.log(res, '<====cameraImgVideoHandler');
            var thumbnailPath = {};
            if (res?.mime == 'video/mp4') {
              thumbnailPath = await createThumbnail({
                url: res?.path,
                timeStamp: 10000, // Specify the timestamp for the desired thumbnail (in milliseconds)
              });
              // setThumbnail(thumbnailPath);
            }
            
            // return;
            // appendMediaPreview(res, thumbnailPath);
            uploadMedia(res, res.path.split('/').pop()); // upload media directly from gallary
          }
        })
        .catch(err => { });
    }
  };

  const onPressMedia = currentMessage => {
    if (
      currentMessage?.mediaType == 'application/pdf' ||
      currentMessage?.mediaType == 'docs'
    ) {
      Linking.openURL(currentMessage?.mediaUrl);
      return;
    }

    setisVisible(true);
    setCurrentMsg(currentMessage);
  };

  const showRoomUser = useCallback(
    props => {
      if (_.isEmpty(roomUsers)) {
        return null;
      }
      return (
        <TouchableOpacity
          activeOpacity={0.7}
          onPress={() => updateState({ showParticipant: true })}>
          <CircularImages size={25} data={roomUsers} />
        </TouchableOpacity>
      );
    },
    [roomUsers],
  );

  const renderMessage = useCallback(props => {
    const { currentMessage } = props;
    let isRight = currentMessage?.auth_user_id == userData?.id;
    if (isRight) {
      return !!currentMessage?.is_media ? (
        <ChatMedia
          currentMessage={currentMessage}
          isRight
          onPressMedia={() => onPressMedia(currentMessage)}
          containerStyle={{
            borderTopLeftRadius: moderateScale(12),
          }}
        />
      ) : (
        <View
          key={String(currentMessage._id)}
          style={{
            ...styles.chatStyle,
            alignSelf: 'flex-end',
            backgroundColor: '#0084ff',
            borderBottomRightRadius: 0,
          }}>
          <View style={{ flexDirection: 'row' }}>
            <View style={{ marginHorizontal: 8, flexShrink: 1 }}>
              <Text
                style={{
                  fontSize: textScale(14),
                  fontFamily: fontFamily.regular,
                  textTransform: 'capitalize',
                  color: colors.white,
                }}>
                {currentMessage?.username}
              </Text>

              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                <Text
                  style={{
                    ...styles.descText,
                    color: colors.white,
                  }}>
                  {currentMessage?.message}
                </Text>
                <Text
                  style={{ ...styles.timeText, color: colors.whiteOpacity77 }}>
                  {moment(currentMessage?.created_date).format('LT')}
                </Text>
              </View>
            </View>
          </View>
        </View>
      );
    }
    return (
      <View>
        {!!currentMessage?.is_media ? (
          <ChatMedia
            currentMessage={currentMessage}
            containerStyle={{
              borderTopRightRadius: moderateScale(12),
            }}
          />
        ) :
          <View style={{ flexDirection: 'row' }}>
            <FastImage
              source={{
                uri: currentMessage?.display_image,
                priority: FastImage.priority.high,
                cache: FastImage.cacheControl.immutable,
              }}
              style={styles.cahtUserImage}
            />
            <View
              key={String(currentMessage._id)}
              style={{
                ...styles.chatStyle,
                alignSelf: 'flex-start',
                backgroundColor: colors.white,
                borderBottomLeftRadius: moderateScale(0),
                maxWidth: width / 1.2,
              }}>
              <View style={{ marginHorizontal: 8, flexShrink: 1 }}>
                <Text
                  style={{
                    fontSize: textScale(14),
                    fontFamily: fontFamily.regular,
                    textTransform: 'capitalize',
                    color: colors.black,
                  }}>
                  {currentMessage?.username}
                </Text>

                <Text
                  style={{
                    ...styles.descText,
                    color: colors.black,
                  }}>
                  {currentMessage?.message}
                </Text>
                <Text style={styles.timeText}>
                  {moment(currentMessage?.created_date).format('LT')}
                </Text>
              </View>
            </View>
          </View>}
      </View>

    );
  }, []);

  const SendButton = useCallback(() => {
    return (
      <View
        style={{
          marginHorizontal: 10,
          alignSelf: 'center',
          height: '100%',
          alignItems: 'center',
          justifyContent: 'center',
        }}>
        <Image source={imagePath.send} />
      </View>
    );
  }, []);

  return (
    <WrapperContainer
      bgColor={colors.white}
      statusBarColor={colors.white}
      isLoading={false}>
      <Header
        leftIcon={imagePath.backArrow}
        centerTitle={`# ${paramData?.room_id || ''}`}
        customRight={showRoomUser}
        onPressLeft={!! paramData?.fromNotification ? () => {
          navigation.reset({
            index: 0,
            routes: [{ name: clientInfo?.is_freelancer ? navigationStrings.BOTTOM_STACK : navigationStrings.DRAWER_ROUTES }],
          })
        }: ()=>navigation.goBack()}
      />

      <ImageBackground source={imagePath.icBgLight} style={{ flex: 1 }}>
        <GiftedChat
          messages={messages}
          onSend={messages => onSend(messages)}
          user={{ _id: userData?.id }}
          renderMessage={renderMessage}
          isKeyboardInternallyHandled={true}
          renderInputToolbar={props => {
            return (
              <InputToolbar
                containerStyle={{ backgroundColor: '#f6f6f6', paddingTop: 0 }}
                {...props}
              />
            );
          }}
          textInputStyle={{
            backgroundColor: '#ffffff',
            paddingTop: Platform.OS == 'ios' ? 10 : undefined,
            borderRadius: 20,
            paddingHorizontal: 20,
            // marginVertical: 30,
            textAlignVertical: 'center',
            fontFamily: fontFamily.regular,
            alignSelf: 'center',
            color: colors.black,
          }}
          renderSend={props => {
            return (
              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                <ButtonImage //Send attachements button
                  onPress={() => actionSheet.current.show()}
                  image={imagePath.icAttachments}
                  btnStyle={{
                    marginLeft: 10,
                  }}
                  imgStyle={{
                    height: moderateScale(25),
                    width: moderateScale(25),
                  }}
                />
                <Send
                  alwaysShowSend
                  containerStyle={{ backgroundColor: 'red' }}
                  children={<SendButton />}
                  {...props}
                />
              </View>
            );
          }}
        />
      </ImageBackground>

      <ActionSheet
        ref={actionSheet}
        // title={'Choose one option'}
        options={[
          strings.CAMERA,
          strings.GALLERY,
          // strings.DOCUMENTS,
          strings.CANCEL,
        ]}
        cancelButtonIndex={2}
        destructiveButtonIndex={2}
        onPress={index => cameraHandle(index)}
      />

      
      <Modal
        isVisible={showParticipant}
        style={{
          margin: 0,
          justifyContent: 'flex-end',
        }}
        onBackdropPress={() => updateState({ showParticipant: false })}>
        <View
          style={{
            ...styles.modalStyle,
            backgroundColor: colors.white,
          }}>
          <Text
            style={{
              fontFamily: fontFamily?.bold,
              fontSize: textScale(16),
              color: colors.black,
            }}>
            {roomUsers.length} Participants
          </Text>

          <TouchableOpacity
            activeOpacity={0.7}
            onPress={() => updateState({ showParticipant: false })}>
            <Image source={imagePath.closeButton} />
          </TouchableOpacity>

          <ScrollView>
            {roomUsers.map((val, i) => {
              return (
                <View
                  style={{
                    marginVertical: moderateScaleVertical(8),
                    flexDirection: 'row',
                    alignItems: 'center',
                  }}>
                  <FastImage
                    source={{
                      uri: val?.display_image,
                      priority: FastImage.priority.high,
                      cache: FastImage.cacheControl.immutable,
                    }}
                    style={{
                      ...styles.imgStyle,
                      backgroundColor: colors.blackOpacity43,
                    }}
                  />
                  <View style={{ marginLeft: moderateScale(8) }}>
                    <Text>
                      {val?.auth_user_id == userData?.id
                        ? 'You'
                        : val?.username}
                    </Text>
                    {!!val?.phone_num ? <Text>{val?.phone_num}</Text> : null}
                  </View>
                </View>
              );
            })}
          </ScrollView>
        </View>
      </Modal>

    </WrapperContainer>

  );
}

const stylesFun = ({ }) => {
  const styles = StyleSheet.create({
    imgStyle: {
      width: moderateScale(35),
      height: moderateScale(35),
      borderRadius: moderateScale(35 / 2),
    },
    modalStyle: {
      padding: moderateScale(10),
      borderTopLeftRadius: moderateScale(8),
      borderTopRightRadius: moderateScale(8),
      maxHeight: height / 2,
    },
    userNameStyle: {
      fontSize: textScale(12),
      fontFamily: fontFamily.medium,
      textTransform: 'capitalize',
    },
    cahtUserImage: {
      width: moderateScale(20),
      height: moderateScale(20),
      borderRadius: moderateScale(10),
      backgroundColor: colors.blackOpacity43,
      marginLeft: 8,
    },
    descText: {
      fontSize: textScale(12),
      fontFamily: fontFamily.regular,
      textTransform: 'capitalize',
      lineHeight: moderateScale(18),
      marginTop: moderateScaleVertical(4),
    },
    timeText: {
      fontSize: textScale(10),
      fontFamily: fontFamily.regular,
      textTransform: 'uppercase',
      color: colors.blackOpacity43,
      marginLeft: moderateScale(12),
      marginTop: moderateScaleVertical(6),
      alignSelf: 'flex-end',
    },
    flexView: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
    },
    chatStyle: {
      paddingVertical: moderateScaleVertical(6),
      borderRadius: moderateScale(8),
      marginBottom: moderateScale(10),
      paddingHorizontal: moderateScale(2),
      maxWidth: width - 16,
      marginHorizontal: moderateScale(8),
    },
  });
  return styles;
};
