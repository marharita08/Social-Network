import React from "react";
import {useMutation} from "react-query";
import PropTypes from 'prop-types';

import AuthComponent from "../../components/authComponent";
import { auth} from "../../api/auth";

const AuthContainer = ({setAuthContext, handleError}) => {

    const options = {
        onSuccess: (data) => {
            const { data: {user, accessToken, refreshToken}} = data;
            setAuthContext({
                authenticated: true,
                user,
                isAdmin: user.role === 'admin',
                accessToken,
                refreshToken
            })
        },
        onError: handleError
    }

    const { mutate: authMutate, isLoading: authLoading } = useMutation(auth, options);

    const onFormSubmit = (data) => {
        authMutate(data);
    }

    const initialUser = {
        email:'',
        password:''
    }

    return (
        <AuthComponent
            onFormSubmit={onFormSubmit}
            initialUser={initialUser}
            authLoading={authLoading}
        />
    )
}

AuthContainer.propTypes = {
    setAuthContext: PropTypes.func.isRequired,
    handleError: PropTypes.func.isRequired,
}

export default AuthContainer;
