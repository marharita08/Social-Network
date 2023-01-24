import React from "react";
import {Formik, Form, Field} from "formik";
import * as Yup from "yup";
import {TextField} from "formik-mui";
import {Button, Card, CardHeader, CircularProgress} from "@mui/material";
import PropTypes from "prop-types";
import LoginIcon from '@mui/icons-material/Login';


const AuthComponent = ({
    onFormSubmit,
    initialUser,
    authLoading }) => {

    const schema = Yup.object().shape({
        email: Yup.string().required("Email is required").email("Email is invalid"),
        password: Yup.string().required("Password is required").min(8, "Password should contain more than 8 symbols"),
    })

    return (
        <div align={"center"} className={'margin'}>
            <Card sx={{width: '500px'}}>
                <CardHeader
                    title={'Login/Registration'}
                />
                <div>
                    <Formik
                        onSubmit={onFormSubmit}
                        validationSchema={schema}
                        initialValues={initialUser}
                    >
                        <Form>
                            <Field
                                component={TextField}
                                type={"email"}
                                name={"email"}
                                label={"Email"}
                                sx={{margin:'5px'}}
                            />
                            <br/>
                            <Field
                                component={TextField}
                                type={"password"}
                                name={"password"}
                                label={"Password"}
                                className={'margin'}
                                sx={{margin:'5px'}}
                            />
                            <br/>
                            <Button
                                variant="contained"
                                type="submit"
                                className={'margin'}
                                startIcon={
                                    authLoading ? (
                                        <CircularProgress color="inherit" size={25}/>
                                    ) : <LoginIcon/>
                                }
                            >
                                Log in
                            </Button>
                        </Form>
                    </Formik>
                </div>
                <br/>
            </Card>
        </div>
    )
}

AuthComponent.propTypes = {
    onFormSubmit: PropTypes.func.isRequired,
    initialUser: PropTypes.shape({
        email: PropTypes.string.isRequired,
        password: PropTypes.string.isRequired,
    }),
    authLoading: PropTypes.bool
}

export default AuthComponent;
