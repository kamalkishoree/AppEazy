import React, { useEffect, useState } from 'react';
import { FlatList, Text, View } from 'react-native';
import { useSelector } from 'react-redux';
import CategoriesCard from '../../../Components/CategoriesCard';
import OoryksHeader from '../../../Components/OoryksHeader';
import WrapperContainer from '../../../Components/WrapperContainer';
import strings from '../../../constants/lang';
import navigationStrings from '../../../navigation/navigationStrings';
import actions from '../../../redux/actions';
import { moderateScale, moderateScaleVertical, textScale } from '../../../styles/responsiveSize';
import { showError } from '../../../utils/helperFunctions';
import stylesFunc from './styles';
import {
    Menu,
    MenuOption,
    MenuOptions,
    MenuTrigger,
} from 'react-native-popup-menu';
import colors from '../../../styles/colors';
import { MyDarkTheme } from '../../../styles/theme';
import imagePath from '../../../constants/imagePath';
import { useDarkMode } from 'react-native-dynamic';
import FastImage from 'react-native-fast-image';
import { cloneDeep } from 'lodash';

const AllCategories = ({ navigation }) => {
    const {
        appData,
        currencies,
        languages,
        themeToggle,
        themeColor,
        appStyle
    } = useSelector(state => state?.initBoot);
    const darkthemeusingDevice = useDarkMode();
    const isDarkMode = themeToggle ? darkthemeusingDevice : themeColor;

    const fontFamily = appStyle?.fontSizeData;


    const [allCategories, setAllCategories] = useState([])
    const [isLoading, setIsLoading] = useState(false)
    const [currSelectedFilter, setcurrSelectedFilter] = useState({ id: 1, type: 'All' })
    const [initCategories, setinitCategories] = useState([])



    const moveToNewScreen =
        (screenName, data = {}) =>
            () => {
                navigation.navigate(screenName, { data });
            };

    useEffect(() => {
        getCategories()
    }, [])

    const getCategories = () => {
        setIsLoading(true)
        actions.getAllCategories({
            type: "p2p"
        }, {
            code: appData?.profile?.code,
            currency: currencies?.primary_currency?.id,
            language: languages?.primary_language?.id,
        }).then((res) => {
            console.log(res, "<===getAllCategories")
            setIsLoading(false)
            setAllCategories(res?.data?.navCategories)
            setinitCategories(res?.data?.navCategories)
        }).catch(errorMethod)
    }

    const errorMethod = error => {
        console.log(error, "<===getAllCategories")
        setIsLoading(false);
        showError(error?.message || error?.error);
    };


    const onSelectedFilter = (selectedFilter) => {
        setcurrSelectedFilter(selectedFilter)
        let allCategories = cloneDeep(initCategories)
        if (selectedFilter?.id == 1) {
            setAllCategories(allCategories)
        }
        else {
            const filteredItems = allCategories.filter(item => item.type_id === selectedFilter?.id);
            setAllCategories(filteredItems)
        }
    };

    const homeAllFilters = () => {
        let homeFilter = [
            { id: 1, type: "All" },
            { id: 10, type: "Rental" },
            { id: 13, type: "Sell" },
        ];

        return homeFilter;
    };


    const customView = () => {
        return <Menu style={{ alignSelf: 'flex-end' }}>
            <MenuTrigger>
                <View style={{
                    flexDirection: 'row',
                    alignItems: 'center',
                    justifyContent: 'center',
                    borderRadius: moderateScale(4),
                    borderWidth: 0.3,
                    borderColor: colors.textGreyB,
                    width: moderateScale(100),
                    height: moderateScale(30)

                }}>
                    <FastImage
                        style={{
                            height: moderateScaleVertical(16),
                            width: moderateScale(16),
                            tintColor: isDarkMode
                                ? MyDarkTheme.colors.white
                                : colors.black,
                        }}
                        resizeMode="contain"
                        source={isDarkMode ? imagePath.sortSelected : imagePath.sort}
                    />
                    <Text
                        style={{
                            fontSize: textScale(12),
                            marginHorizontal: moderateScale(5),
                            fontFamily: fontFamily.regular,
                            color: isDarkMode
                                ? MyDarkTheme.colors.text
                                : colors.black,
                        }}>
                        {!currSelectedFilter
                            ? strings.RELEVANCE
                            : currSelectedFilter?.type}
                    </Text>
                </View>
            </MenuTrigger>
            <MenuOptions
                customStyles={{
                    optionsContainer: {
                        marginTop: moderateScaleVertical(36),
                        width: moderateScale(100),
                    },
                }}>
                {homeAllFilters()?.map((item, index) => {
                    return (
                        <View key={index}>
                            <MenuOption
                                onSelect={() => onSelectedFilter(item)}
                                key={String(index)}
                                text={item?.type}
                                style={{
                                    marginVertical: moderateScaleVertical(5),
                                }}
                            />
                            <View
                                style={{
                                    borderBottomWidth: 1,
                                    borderBottomColor: colors.greyColor,
                                }}
                            />
                        </View>
                    );
                })}
            </MenuOptions>
        </Menu>
    }




    const renderItem = ({ item, index }) => {
        return (
            <CategoriesCard item={item} onPress={moveToNewScreen(navigationStrings.P2P_PRODUCTS, item)} />
        )
    }


    return (
        <WrapperContainer isLoading={isLoading} bgColor={isDarkMode?MyDarkTheme.colors.background:colors.white}>
            <OoryksHeader leftTitle={strings.CATEGORIES} isCustomView customView={customView} />
            <View style={{ marginTop: moderateScaleVertical(12), paddingHorizontal: moderateScale(16) }}>
                <FlatList
                    data={allCategories}
                    renderItem={renderItem}
                    numColumns={2}
                    showsVerticalScrollIndicator={false}
                    columnWrapperStyle={{
                        justifyContent: "space-between"
                    }}
                    ItemSeparatorComponent={() => {
                        return (
                            <View style={{ height: moderateScale(12) }} />
                        )
                    }}
                    ListFooterComponent={() => <View style={{
                        height: moderateScaleVertical(80)
                    }} />}
                />
            </View>
        </WrapperContainer>
        
    );
};





//make this component available to the app
export default AllCategories;
