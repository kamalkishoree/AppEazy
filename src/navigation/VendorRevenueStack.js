import { createNativeStackNavigator } from '@react-navigation/native-stack';
import React from 'react';
import {RoyoHome, RoyoOrder, VendorList, VendorRevenue} from '../Screens';
import navigationStrings from './navigationStrings';

const Stack = createNativeStackNavigator();
export default function () {
  return (
    <Stack.Navigator
      screenOptions={{
        headerShown: false,
      }}>
      {/* <Stack.Screen
        name={navigationStrings.VENDOR_REVENUE}
        component={VendorRevenue}
      /> */}
      <Stack.Screen
        name={navigationStrings.VENDOR_REVENUE}
        component={RoyoHome}
      />
      <Stack.Screen
        name={navigationStrings.VENDORLIST}
        component={VendorList}
      />
      <Stack.Screen
        name={navigationStrings.ROYO_VENDOR_ORDER}
        component={RoyoOrder}
      />
    </Stack.Navigator>
  );
}
