import { getBundleId } from "react-native-device-info";
import { appIds, shortCodes } from "../../utils/constants/DynamicAppKeys";

export const getAppCode = () => {
    switch (getBundleId()) {
        case appIds.superApp: return shortCodes.superApp;
        default: return '245bae'
    }
}