import org.jenkinsci.plugins.pipeline.modeldefinition.Utils
pipeline {
    agent none
    environment {
        CLUSTER_STAGING = 'kubernetes-iii-staging'
        ENTRYPOINT      = '/home/jenkins/entrypoint/entrypoint.sh'
    }
    stages {
        stage("Build Dockerfile") {
            agent {
                kubernetes {
                  cloud "${CLUSTER_STAGING}"
                  inheritFrom 'jenkins-agent'
                }
            }
            steps {
                container('deployer') {
                    sh "${ENTRYPOINT}"
                    sh "/bin/bash .jenkins/build.sh swift ${SWIFT_TAG}"
                }
            }
        }
    } 
}